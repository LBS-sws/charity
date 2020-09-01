<?php

class SearchCreditList extends CListPageModel
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
            'id'=>Yii::t('integral','ID'),
            'employee_id'=>Yii::t('charity','Employee Name'),
            'employee_code'=>Yii::t('charity','Employee Code'),
            'employee_name'=>Yii::t('charity','Employee Name'),
            'credit_type'=>Yii::t('charity','Charity Name'),
            'credit_name'=>Yii::t('charity','Charity Name'),
            'credit_point'=>Yii::t('charity','Charity Num'),
            'city'=>Yii::t('charity','City'),
            'city_name'=>Yii::t('charity','City'),
            'state'=>Yii::t('charity','Status'),
            'apply_date'=>Yii::t('charity','apply for time'),
            'exp_date'=>Yii::t('charity','expiration date'),
        );
    }

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, searchTimeStart, searchTimeEnd, dateRangeValue','safe',),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        if(Yii::app()->user->validFunction('ZR03')){ //查詢所有員工
            $citySql = " a.id>0 ";
        }else{
            $citySql = " d.city IN ($city_allow) ";
        }
        $sql1 = "select a.*,b.charity_name,b.validity,d.name AS employee_name,d.code AS employee_code,d.city AS s_city from cy_credit_request a
                LEFT JOIN cy_credit_type b ON a.credit_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where $citySql AND a.state = 3 and d.staff_status = 0 
			";
        $sql2 = "select count(a.id) from cy_credit_request a
                LEFT JOIN cy_credit_type b ON a.credit_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where $citySql AND a.state = 3 and d.staff_status = 0 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'employee_code':
                    $clause .= General::getSqlConditionClause('d.code',$svalue);
                    break;
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('d.name',$svalue);
                    break;
                case 'credit_name':
                    $clause .= General::getSqlConditionClause('b.charity_name',$svalue);
                    break;
                case 'credit_point':
                    $clause .= General::getSqlConditionClause('a.credit_point',$svalue);
                    break;
                case 'city_name':
                    $clause .= ' and d.city in '.RequestCreditList::getCityCodeSqlLikeName($svalue);
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
            $order = " order by a.apply_date desc";

        $sql = $sql2.$clause;
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = $sql1.$clause.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                $record['validity']=$record['validity']>0?$record['validity']-1:$record['validity'];
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'employee_code'=>$record['employee_code'],
                    'employee_name'=>$record['employee_name'],
                    'credit_name'=>$record['charity_name'],
                    'credit_point'=>$record['credit_point'],
                    'apply_date'=>date("Y-m-d",strtotime($record['apply_date'])),
                    'exp_date'=>date("Y-12-31",strtotime($record['apply_date']." + ".$record['validity']." year")),
                    'city'=>CGeneral::getCityName($record["s_city"]),
                );
            }
        }
        $session = Yii::app()->session;
        $session['searchCredit_op01'] = $this->getCriteria();
        return true;
    }
}
