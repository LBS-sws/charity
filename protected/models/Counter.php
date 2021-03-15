<?php
class Counter {
	
	//申请慈善分
	public static function getApplyCredit() {
        //$uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $staffId = Yii::app()->user->staff_id();//
        $imDo_num = Yii::app()->db->createCommand()->select("count(a.id)")->from("cy_credit_request a")
            ->leftJoin("hr$suffix.hr_employee d","a.employee_id = d.id")
            ->where('state=2 and d.id=:id',array(":id"=>$staffId))->queryScalar();
        return $imDo_num;
	}

	//慈善分兑换
    public static function  getApplyPrize(){
        $suffix = Yii::app()->params['envSuffix'];
        $staffId = Yii::app()->user->staff_id();//
        $imDo_num = Yii::app()->db->createCommand()->select("count(a.id)")->from("cy_prize_request a")
            ->leftJoin("hr$suffix.hr_employee d","a.employee_id = d.id")
            ->where('state=2 and d.id=:id',array(":id"=>$staffId))->queryScalar();
        return $imDo_num;
    }

	//申请慈善分审核
	public static function getAuditCredit() {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $imDo_num = Yii::app()->db->createCommand()->select("count(a.id)")->from("cy_credit_request a")
            ->leftJoin("hr$suffix.hr_employee d","a.employee_id = d.id")
            ->where("d.city IN ($city_allow) AND a.state = 1")->queryScalar();
        return $imDo_num;
	}

	//慈善分兑换审核
    public static function  getAuditPrize(){
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $imDo_num = Yii::app()->db->createCommand()->select("count(a.id)")->from("cy_prize_request a")
            ->leftJoin("hr$suffix.hr_employee d","a.employee_id = d.id")
            ->where("d.city IN ($city_allow) AND a.state = 1")->queryScalar();
        return $imDo_num;
    }

}
?>