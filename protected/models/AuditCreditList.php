<?php

class AuditCreditList extends CListPageModel
{
	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('integral','ID'),
            'employee_id'=>Yii::t('charity','Employee Name'),
            'employee_name'=>Yii::t('charity','Employee Name'),
            'credit_type'=>Yii::t('charity','Charity Name'),
            'charity_name'=>Yii::t('charity','Charity Name'),
            'credit_point'=>Yii::t('charity','Charity Num'),
            'city'=>Yii::t('charity','City'),
            'city_name'=>Yii::t('charity','City'),
            'state'=>Yii::t('charity','Status'),//狀態 0：草稿 1：發送  2：拒絕  3：完成  4:確定
            'apply_date'=>Yii::t('charity','apply for time'),
            'category'=>Yii::t('charity','Charity type'),
        );
	}
	
	public function retrieveDataByPage($pageNum=1,$type=2)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $sql1 = "select a.*,b.charity_name,d.name AS employee_name,d.city AS s_city from cy_credit_request a
                LEFT JOIN cy_credit_type b ON a.credit_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where (d.city IN ($city_allow) AND a.state = 1) and type_state='$type' 
			";
        $sql2 = "select count(a.id) from cy_credit_request a
                LEFT JOIN cy_credit_type b ON a.credit_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where (d.city IN ($city_allow) AND a.state = 1) and type_state='$type' 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('d.name',$svalue);
                    break;
                case 'charity_name':
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
                $colorList = $this->getListStatus($record['state']);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'employee_name'=>$record['employee_name'],
                    'charity_name'=>$record['charity_name'],
                    'credit_point'=>$record['credit_point'],
                    'type'=>$type,
                    'apply_date'=>date("Y-m-d",strtotime($record['apply_date'])),
                    'status'=>$colorList["status"],
                    'city'=>CGeneral::getCityName($record["s_city"]),
                    'style'=>$colorList["style"],
                );
			}
		}
		$session = Yii::app()->session;
		$session['auditCredit_ya0'.$type] = $this->getCriteria();
		return true;
	}


    public function getListStatus($status){
        switch ($status){
            case 1:
                return array(
                    "status"=>Yii::t("charity","pending approval"),
                    "style"=>" text-yellow"
                );//已提交，待審核
            default:
                return array(
                    "status"=>Yii::t("charity","Error"),
                    "style"=>" "
                );//已拒絕
        }
    }
}
