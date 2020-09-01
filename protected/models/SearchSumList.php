<?php

class SearchSumList extends CListPageModel
{
    public $year;//年
    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('charity','ID'),
            'employee_id'=>Yii::t('charity','Employee Name'),
            'employee_code'=>Yii::t('charity','Employee Code'),
            'employee_name'=>Yii::t('charity','Employee Name'),
            'year'=>Yii::t('charity','particular year'),
            'start_num'=>Yii::t('charity','Sum Gift'),
            'end_num'=>Yii::t('charity','Available Gift'),
            'city'=>Yii::t('charity','City'),
            'city_name'=>Yii::t('charity','City'),
        );
    }

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, year','safe',),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $sql1 = "select a.year,d.code AS employee_code,d.name AS employee_name,d.city AS s_city,SUM(a.start_num) AS start_num,SUM(a.end_num) AS end_num from cy_credit_point a
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow)  and d.staff_status = 0 
			";
        $sql2 = "select a.year,d.name AS employee_name,d.city AS s_city,SUM(a.start_num) AS start_num,SUM(a.end_num) AS end_num from cy_credit_point a
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow)  and d.staff_status = 0 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('d.name',$svalue);
                    break;
                case 'city_name'://
                    $clause .= ' and d.city in '.CreditRequestList::getCityCodeSqlLikeName($svalue);
                    break;
            }
        }
        if (!empty($this->year)) {
            $year = str_replace("'","\'",$this->year);
            if(!is_numeric($year)){
                $year = date("Y");
            }
            $clause .= " and a.year = '$year' ";
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        } else
            $order = " order by end_num desc";


        $group = "GROUP BY a.employee_id,a.year ";

        $sql = $sql1.$clause.$group;
        $count = Yii::app()->db->createCommand($sql)->queryAll();
        if($count){
            $this->totalRow = count($count);
        }else{
            $this->totalRow = 0;
        }

        $sql = $sql1.$clause.$group.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        $list = array();
        $this->attr = array();
        if (count($records) > 0) {
            $maxYear = intval(date("Y"))-5;
            foreach ($records as $k=>$record) {
                $this->attr[] = array(
                    'employee_code'=>$record['employee_code'],
                    'employee_name'=>$record['employee_name'],
                    'start_num'=>$record['start_num'],
                    'end_num'=>$record['end_num'],
                    'year'=>$record['year'].Yii::t("charity","year"),
                    'city'=>CGeneral::getCityName($record["s_city"]),
                    'style'=>intval($record['year'])<=$maxYear?"text-danger":"",
                );
            }
        }
        $session = Yii::app()->session;
        $session['searchSum_op01'] = $this->getCriteria();
        return true;
    }

    public function getYearList(){
        $sql = "select year from cy_credit_point GROUP BY year ORDER by year asc";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();

        $arr=array();
        $maxYear = intval(date("Y"))-5;
        foreach ($rows as $row){
            $arr[]=array('value'=>$row["year"],'name'=>$row["year"].Yii::t("charity","year"),"color"=>intval($row['year'])<=$maxYear?"#a94442":"#555");
        }
        return $arr;
    }
}
