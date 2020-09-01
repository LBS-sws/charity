<?php

class CreditTypeList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
            'charity_code'=>Yii::t('charity','Charity Code'),
			'charity_name'=>Yii::t('charity','Charity Name'),
            'charity_point'=>Yii::t('charity','Charity Num'),
            'rule'=>Yii::t('charity','conditions'),
            'validity'=>Yii::t('charity','validity'),
            'bumen_ex'=>Yii::t('charity','Scope of application'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from cy_credit_type
				where id >= 0 
			";
		$sql2 = "select count(id)
				from cy_credit_type
				where id >= 0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'charity_code':
					$clause .= General::getSqlConditionClause('charity_code', $svalue);
					break;
				case 'charity_name':
					$clause .= General::getSqlConditionClause('charity_name', $svalue);
					break;
				case 'charity_point':
					$clause .= General::getSqlConditionClause('charity_point', $svalue);
					break;
				case 'validity':
					$clause .= General::getSqlConditionClause('validity', $svalue);
					break;
				case 'rule':
					$clause .= General::getSqlConditionClause('rule', $svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by id desc";

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $arr = array();
                $record['bumen_ex']=explode(",",$record['bumen_ex']);
                if (is_array($record['bumen_ex'])){
                    foreach ($record['bumen_ex'] as $item){
                        if(count($arr)==3){
                            $arr[] = "....";
                            break;
                        }elseif (!empty($item)){
                            $arr[] = $item;
                        }
                    }
                }
                $record['bumen_ex'] = implode(",",$arr);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'charity_code'=>$record['charity_code'],
                    'charity_name'=>$record['charity_name'],
                    'charity_point'=>$record['charity_point'],
                    'bumen_ex'=>$record['bumen_ex'],
                    'validity'=>$record['validity'],
                    'rule'=>$record['rule'],
                );
			}
		}
		$session = Yii::app()->session;
		$session['CreditType_op01'] = $this->getCriteria();
		return true;
	}

}
