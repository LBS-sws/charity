<?php

class AuditPrizeList extends CListPageModel
{
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
	
	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $sql1 = "select a.*,(a.prize_point*a.apply_num) as total_point,b.prize_name,d.name AS employee_name,d.city AS s_city from cy_prize_request a
                LEFT JOIN cy_prize_type b ON a.prize_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow) and a.state = 1 
			";
        $sql2 = "select count(a.id) from cy_prize_request a
                LEFT JOIN cy_prize_type b ON a.prize_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow) and a.state = 1 
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
                    'prize_name'=>$record['prize_name'],
                    'total_point'=>$record['total_point'],
                    'apply_num'=>$record['apply_num'],
                    'prize_point'=>$record['prize_point'],
                    'apply_date'=>date("Y-m-d",strtotime($record['apply_date'])),
                    'status'=>$colorList["status"],
                    'city'=>CGeneral::getCityName($record["s_city"]),
                    'style'=>$colorList["style"],
                );
			}
		}
		$session = Yii::app()->session;
		$session['auditPrize_ya01'] = $this->getCriteria();
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
