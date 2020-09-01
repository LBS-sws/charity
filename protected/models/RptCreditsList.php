<?php
class RptCreditsList extends CReport {
	protected function fields() {
		return array(
			'employee_code'=>array('label'=>Yii::t('charity','Employee Code'),'width'=>22,'align'=>'L'),
			'employee_name'=>array('label'=>Yii::t('charity','Employee Name'),'width'=>22,'align'=>'L'),
            's_city'=>array('label'=>Yii::t('charity','City'),'width'=>20,'align'=>'L'),
			'charity_name'=>array('label'=>Yii::t('charity','Charity Name'),'width'=>30,'align'=>'L'),
			'credit_point'=>array('label'=>Yii::t('charity','Charity Num'),'width'=>25,'align'=>'C'),
			'apply_date'=>array('label'=>Yii::t('charity','apply for time'),'width'=>15,'align'=>'L'),
			//'end_date'=>array('label'=>Yii::t('charity','expiration date'),'width'=>15,'align'=>'L'),
		);
	}
	
	public function genReport() {
		$this->retrieveData();
		$this->title = $this->getReportName();
		$this->subtitle = Yii::t('app','Charity Credit Report').':'.$this->criteria['START_DT'].' - '.$this->criteria['END_DT'].' / '
			.Yii::t('charity','Staffs').':'.$this->criteria['STAFFSDESC'].' / '
            .Yii::t('report','City').':'.$this->criteria['CITYDESC'];
		return $this->exportExcel();
	}

	public function retrieveData() {
        $start_dt = $this->criteria['START_DT'];
        $end_dt = $this->criteria['END_DT'];
		$city = $this->criteria['CITY'];
		$staff_id = $this->criteria['STAFFS'];
        $city_allow = $this->criteria['city_allow'];
        $auth_bool = $this->criteria['auth_bool'];//0:沒有所有地區權限，1：所有地區
        if($auth_bool == 1){
            $citySql = " a.id>0 ";
        }else{
            $citySql = " d.city in($city_allow) ";
        }

        $cond_city = "";
        if (!empty($city)) {
            $citylist = explode('~',$city);
            if(count($citylist)>1){
                $cond_city = implode("','",$citylist);
            }else{
                $cond_city = "'".reset($citylist)."'";
            }
            if ($cond_city!=''){
                $cond_city = " and d.city in ($cond_city) ";
            }
        }
		
		$suffix = Yii::app()->params['envSuffix'];

		$cond_time = "";
		if(!empty($start_dt)){
		    $start_dt = date("Y-m-d",strtotime($start_dt));
		    $cond_time.=" and a.apply_date>='$start_dt' ";
        }
		if(!empty($end_dt)){
            $end_dt = date("Y-m-d",strtotime($end_dt));
		    $cond_time.=" and a.apply_date<='$end_dt' ";
        }

		$cond_staff = '';
		if (!empty($staff_id)) {
			$ids = explode('~',$staff_id);
			if(count($ids)>1){
                $cond_staff = implode(",",$ids);
            }else{
                $cond_staff = $staff_id;
            }
			if ($cond_staff!=''){
                $cond_staff = " and a.employee_id in ($cond_staff) ";
            } 
		}
        $sql = "select a.*,d.code AS employee_code,d.name AS employee_name,d.city AS s_city,e.charity_name  
                from cy_credit_request a 
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                LEFT JOIN cy_credit_type e ON a.credit_type = e.id
                where $citySql and a.state=3  and d.staff_status = 0 
                $cond_staff $cond_time $cond_city 
				order by d.city desc, a.id desc
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
				$temp['employee_code'] = $row['employee_code'];
				$temp['employee_name'] = $row['employee_name'];
                $temp['s_city'] = CGeneral::getCityName($row['s_city']);
				$temp['charity_name'] = $row['charity_name'];
				$temp['credit_point'] = $row['credit_point'];
                $temp['apply_date'] = CGeneral::toDate($row['apply_date']);
				$this->data[] = $temp;
			}
		}
		return true;
	}
	
	public function getReportName() {
		$city_name = '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil')).$city_name;
	}
}
?>