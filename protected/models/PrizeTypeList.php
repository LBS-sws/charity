<?php

class PrizeTypeList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'prize_name'=>Yii::t('charity','Prize Name'),
			'prize_point'=>Yii::t('charity','Need Prize Point'),
			'z_index'=>Yii::t('charity','z_index'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from cy_prize_type
				where id >= 0 
			";
		$sql2 = "select count(id)
				from cy_prize_type
				where id >= 0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'prize_name':
					$clause .= General::getSqlConditionClause('prize_name', $svalue);
					break;
				case 'prize_point':
					$clause .= General::getSqlConditionClause('prize_point', $svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by z_index desc";

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
						'prize_name'=>$record['prize_name'],
						'prize_point'=>$record['prize_point'],
						'z_index'=>$record['z_index'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['prizeType_op01'] = $this->getCriteria();
		return true;
	}
}
