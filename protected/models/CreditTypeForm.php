<?php

class CreditTypeForm extends CFormModel
{
	public $id;
	public $charity_code;
	public $charity_name;
	public $charity_point;

	public $city;
	public $bumen;
	public $bumen_ex;
	public $rule;
	public $z_index=0;
	public $year_sw=0;
	public $year_max=0;
	public $validity=5;
	public $remark;

	public function attributeLabels()
	{
		return array(
            'charity_code'=>Yii::t('charity','Charity Code'),
            'charity_name'=>Yii::t('charity','Charity Name'),
            'charity_point'=>Yii::t('charity','Charity Num'),
            'rule'=>Yii::t('charity','conditions'),
            'validity'=>Yii::t('charity','validity'),
            'bumen'=>Yii::t('charity','Scope of application'),
            'bumen_ex'=>Yii::t('charity','Scope of application'),

            'year_sw'=>Yii::t('charity','Age limit'),
            'year_max'=>Yii::t('charity','Limited number'),
            'z_index'=>Yii::t('charity','z_index'),
            'remark'=>Yii::t('charity','Remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,charity_code,charity_name,charity_point,rule,year_sw,year_max,remark,z_index,bumen,bumen_ex','safe'),
            array('charity_code','required'),
            array('charity_name','required'),
            array('charity_point','required'),
            array('year_sw','required'),
            array('year_max','validateYearNum'),
            array('year_sw', 'in', 'range' => array(0, 1)),
            array('charity_name','validateName'),
            array('charity_code','validateCode'),
            array('charity_point', 'numerical', 'min'=>0, 'integerOnly'=>true),
            array('z_index', 'numerical', 'integerOnly'=>true),
		);
	}

	public function validateCode($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("cy_credit_type")
            ->where('charity_code=:charity_code and id!=:id', array(':charity_code'=>$this->charity_code,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('charity','Charity Code').Yii::t('charity',' can not repeat');
            $this->addError($attribute,$message);
        }
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("cy_credit_type")
            ->where('charity_name=:charity_name and id!=:id and (bumen="" or FIND_IN_SET(:bumen,bumen))',
                array(':charity_name'=>$this->charity_name,':bumen'=>$this->bumen,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('charity','Charity Name').Yii::t('charity',' can not repeat');
            $this->addError($attribute,$message);
        }
	}

	public function validateYearNum($attribute, $params){
	    if($this->year_sw == 1){
	        if(!is_numeric($this->year_max)){
                $message = Yii::t('charity','Limited number').Yii::t('charity',' Must be Numbers');
                $this->addError($attribute,$message);
            }else{
	            if(intval($this->year_max)!=floatval($this->year_max)){
                    $message = "限制次数只能为整數";
                    $this->addError($attribute,$message);
                }
            }
        }
	}

	public function retrieveData($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("cy_credit_type")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->charity_code = $row['charity_code'];
                $this->charity_name = $row['charity_name'];
                $this->charity_point = $row['charity_point'];
                $this->bumen = $row['bumen'];
                $this->bumen_ex = $row['bumen_ex'];
                $this->rule = $row['rule'];
                $this->year_sw = $row['year_sw'];
                $this->year_max = $row['year_max'];
                $this->z_index = $row['z_index'];
                $this->validity = $row['validity'];
                $this->remark = $row['remark'];
                break;
			}
		}
		return true;
	}

    //獲取積分類型列表
    public function getCreditTypeList($pos_id=''){
	    $arr = array(
	        ""=>array("name"=>"","num"=>"","rule"=>"")
        );
	    $searchSql = "";
	    if(is_numeric($pos_id)){
            $searchSql = " and (bumen='' or FIND_IN_SET($pos_id,bumen))";
        }
        $rs = Yii::app()->db->createCommand()->select()->from("cy_credit_type")
            ->where("charity_point>0 $searchSql")->order("z_index desc")->queryAll();
        if($rs){
            foreach ($rs as $row){//&amp;nbsp;
                $arr[$row["id"]] =array("name"=>$row["charity_code"]." - ".$row["charity_name"],"num"=>$row["charity_point"],"rule"=>$row["rule"]);
            }
        }
        return $arr;
    }

    //獲取積分類型詳情
    public function getCreditTypeListToCreditType($creditType=""){
        $arr = array();
        if(!empty($creditType)){
            $rs = Yii::app()->db->createCommand()->select()->from("cy_credit_type")->where("id=:id",array(":id"=>$creditType))->queryRow();
            if($rs){
                $arr = $rs;
            }
        }
        return $arr;
    }

    //部門查詢
    public function searchDepartment($department){
        $suffix = Yii::app()->params['envSuffix'];
        $arr = array();
        $sql = "";
        if(!empty($department)){
            $sql.="and (a.name like '%$department%' or b.name like '%$department%') ";
        }
        $rows = Yii::app()->db->createCommand()->select("a.*,b.name as city_name")->from("hr$suffix.hr_dept a")
            ->leftjoin("security$suffix.sec_city b","b.code = a.city")
            ->where("type = 0 $sql")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"]."（".$row["city_name"]."）";
            }
        }
        return $arr;
    }

    //刪除驗證
    public function deleteValidate(){
        return true;
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from cy_credit_type where id = :id";
                break;
            case 'new':
                $sql = "insert into cy_credit_type(
							charity_name,charity_code,charity_point, bumen, bumen_ex, rule, remark, year_sw, year_max, validity, z_index, lcu, city
						) values (
							:charity_name,:charity_code,:charity_point, :bumen, :bumen_ex, :rule, :remark, :year_sw, :year_max, 5, :z_index, :lcu, :city
						)";
                break;
            case 'edit':
                $sql = "update cy_credit_type set
							charity_name = :charity_name, 
							charity_code = :charity_code, 
							charity_point = :charity_point, 
							bumen = :bumen, 
							bumen_ex = :bumen_ex, 
							rule = :rule, 
							remark = :remark, 
							year_sw = :year_sw, 
							year_max = :year_max, 
							validity = 5, 
							z_index = :z_index, 
							luu = :luu
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        //$city = Yii::app()->user->city();
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':charity_code')!==false)
            $command->bindParam(':charity_code',$this->charity_code,PDO::PARAM_STR);
        if (strpos($sql,':charity_name')!==false)
            $command->bindParam(':charity_name',$this->charity_name,PDO::PARAM_STR);
        if (strpos($sql,':bumen_ex')!==false)
            $command->bindParam(':bumen_ex',$this->bumen_ex,PDO::PARAM_STR);
        if (strpos($sql,':bumen')!==false)
            $command->bindParam(':bumen',$this->bumen,PDO::PARAM_STR);
        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
        if (strpos($sql,':rule')!==false)
            $command->bindParam(':rule',$this->rule,PDO::PARAM_STR);
        if (strpos($sql,':charity_point')!==false)
            $command->bindParam(':charity_point',$this->charity_point,PDO::PARAM_INT);
        if (strpos($sql,':year_sw')!==false)
            $command->bindParam(':year_sw',$this->year_sw,PDO::PARAM_INT);
        if (strpos($sql,':year_max')!==false)
            $command->bindParam(':year_max',$this->year_max,PDO::PARAM_INT);
        if (strpos($sql,':z_index')!==false)
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
		return true;
	}

    private function lenStr(){
        $code = strval($this->id);
        $this->charity_code = "C";
        for($i = 0;$i < 5-strlen($code);$i++){
            $this->charity_code.="0";
        }
        $this->charity_code .= $code;
    }
}
