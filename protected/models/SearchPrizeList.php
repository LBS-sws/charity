<?php

class SearchPrizeList extends CListPageModel
{
    public $searchTimeStart;//開始日期
    public $searchTimeEnd;//結束日期
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
            'employee_name'=>Yii::t('charity','Employee Name'),
            'prize_name'=>Yii::t('charity','Prize Name'),
            'prize_point'=>Yii::t('charity','Prize Point'),
            'city'=>Yii::t('charity','City'),
            'city_name'=>Yii::t('charity','City'),
            'state'=>Yii::t('charity','Status'),
            'apply_date'=>Yii::t('charity','apply for time'),
            'apply_num'=>Yii::t('charity','apply for number'),
        );
    }

    public function rules()
    {
        return array(
            array('attr, pageNum, dateRangeValue, noOfItem, totalRow, searchField, searchValue, orderField, orderType, searchTimeStart, searchTimeEnd','safe',),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
        if(Yii::app()->user->validFunction('ZR03')){ //查詢所有員工
            $citySql = " a.id>0 ";
        }else{
            $citySql = " d.city IN ($city_allow) ";
        }
        $sql1 = "select a.*,(a.prize_point*a.apply_num) as total_point,b.prize_name,d.name AS employee_name,d.city AS s_city from cy_prize_request a
                LEFT JOIN cy_prize_type b ON a.prize_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where $citySql AND a.state = 3 and d.staff_status = 0 
			";
        $sql2 = "select count(a.id) from cy_prize_request a
                LEFT JOIN cy_prize_type b ON a.prize_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where $citySql AND a.state = 3 and d.staff_status = 0 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('d.name',$svalue);
                    break;
                case 'prize_name':
                    $clause .= General::getSqlConditionClause('b.prize_name',$svalue);
                    break;
                case 'prize_point':
                    $clause .= General::getSqlConditionClause('total_point',$svalue);
                    break;
                case 'city_name':
                    $clause .= ' and d.city in '.CreditRequestList::getCityCodeSqlLikeName($svalue);
                    break;
            }
        }
        if (!empty($this->searchTimeStart) && !empty($this->searchTimeStart)) {
            $svalue = str_replace("'","\'",$this->searchTimeStart);
            $clause .= " and date_format(a.apply_date,'%Y/%m/%d') >='$svalue' ";
        }
        if (!empty($this->searchTimeEnd) && !empty($this->searchTimeEnd)) {
            $svalue = str_replace("'","\'",$this->searchTimeEnd);
            $clause .= " and date_format(a.apply_date,'%Y/%m/%d') <='$svalue' ";
        }
        if (empty($this->searchTimeStart) && empty($this->searchTimeEnd)) {
            $clause .= $this->getDateRangeCondition('a.apply_date');
        } else {
            $this->dateRangeValue = '0';
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        } else
            $order = " order by a.id desc";

        $sql = $sql2.$clause;
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = $sql1.$clause.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'employee_name'=>$record['employee_name'],
                    'prize_name'=>$record['prize_name'],
                    'total_point'=>$record['total_point'],
                    'apply_num'=>$record['apply_num'],
                    'prize_point'=>$record['prize_point'],
                    'apply_date'=>date("Y-m-d",strtotime($record['apply_date'])),
                    'city'=>CGeneral::getCityName($record["s_city"]),
                );
            }
        }
        $session = Yii::app()->session;
        $session['searchPrize_op01'] = $this->getCriteria();
        return true;
    }
}
