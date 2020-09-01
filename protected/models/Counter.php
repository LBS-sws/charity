<?php
class Counter {
    //獲取訂單需要處理的數據
	
	//快速訂單的數量
	public static function getFastNum() {
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $fast_num = Yii::app()->db->createCommand()->select("count(id)")
            ->from("opr_order")->where('status="sent" and status_type=1 and order_class="Fast" and judge=1')->queryScalar();
		return $fast_num;
	}

	//採購活動的數量
    public function getImdoNum(){
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $imDo_num = Yii::app()->db->createCommand()->select("count(id)")
            ->from("opr_order")->where('status="sent" and status_type=1  and order_class!="Fast" and judge=1')->queryScalar();
		return $imDo_num;
    }
	
	//地區審核數量
    public function getAreaNum(){
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $area_num = Yii::app()->db->createCommand()->select("count(id)")
            ->from("opr_order")->where('status="sent" and status_type=0 and city=:city and judge=1',array(":city"=>$city))->queryScalar();
		return $area_num;
    }

	//地區待收貨的數量
    public function getTakeNum(){
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $take_num = Yii::app()->db->createCommand()->select("count(id)")
            ->from("opr_order")->where('status="approve" and judge=1 and city=:city',array(":city"=>$city))->queryScalar();
		return $take_num;
    }

	//地區發貨的數量
    public function getDeliNum(){
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $deli_num = Yii::app()->db->createCommand()->select("count(id)")
            ->from("opr_order")->where('status="sent" and judge=0 and city=:city',array(":city"=>$city))->queryScalar();
		return $deli_num;
    }

	//技術員收貨的數量
    public function getGoodsNum(){
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $goods_num = Yii::app()->db->createCommand()->select("count(id)")
            ->from("opr_order")->where('status="approve" and judge=0 and city=:city and lcu=:lcu',array(":city"=>$city,":lcu"=>$uid))->queryScalar();
		return $goods_num;
    }

	// 营业报告审核的數量
    public function getRepNum(){
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
		$suffix = Yii::app()->params['envSuffix'];
		$type = Yii::app()->user->validFunction('YN01') ? 'PA' : 'PH';
		$wf = new WorkflowOprpt;
		$wf->connection = Yii::app()->db;
		$list = $wf->getPendingRequestIdList('OPRPT', $type, $uid);
		if (empty($list)) $list = '0';
		$cityallow = Yii::app()->user->city_allow();
		$sql = "select count(a.id)
				from opr_monthly_hdr a, security$suffix.sec_city b 
				where a.city in ($cityallow) and a.city=b.code 
				and a.id in ($list)
			";
		$rep_num = Yii::app()->db->createCommand($sql)->queryScalar();
		
		return $rep_num;
    }
}
?>