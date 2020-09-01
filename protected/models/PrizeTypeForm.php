<?php

class PrizeTypeForm extends CFormModel
{
	public $id;
	public $prize_name;
	public $prize_point = 0;
	public $z_index = 0;
	public $prize_remark;


    public $no_of_attm = array(
        'cprize'=>0
    );
    public $docType = 'CPRIZE';
    public $docMasterId = array(
        'cprize'=>0
    );
    public $files;
    public $removeFileId = array(
        'cprize'=>0
    );
	public function attributeLabels()
	{
		return array(
            'prize_name'=>Yii::t('charity','Prize Name'),
            'prize_point'=>Yii::t('charity','Need Prize Point'),
            'z_index'=>Yii::t('charity','z_index'),
            'prize_remark'=>Yii::t('charity','prize remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, prize_name, prize_remark, prize_point, z_index','safe'),
            array('prize_name','required'),
            array('prize_point','required'),
            array('prize_point', 'numerical', 'min'=>0, 'integerOnly'=>true),
            array('z_index', 'numerical', 'min'=>0, 'integerOnly'=>true),
            array('prize_name','validateName'),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("cy_prize_type")
            ->where('prize_name=:prize_name and id!=:id', array(':prize_name'=>$this->prize_name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('integral','the name of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
		$rows = Yii::app()->db->createCommand()->select("*,docman$suffix.countdoc('CPRIZE',id) as cprizedoc")
            ->from("cy_prize_type")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->prize_name = $row['prize_name'];
                $this->prize_point = $row['prize_point'];
                $this->prize_remark = $row['prize_remark'];
                $this->z_index = $row['z_index'];
                $this->no_of_attm['cprize'] = $row['cprizedoc'];
                break;
			}
		}
		return true;
	}

	//根據id獲取兑换名稱
	public function getPrizeNameToId($index) {
        $row = Yii::app()->db->createCommand()->select("prize_name")
            ->from("cy_prize_type")->where("id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
		    return $row['prize_name'];
		}
		return $index;
	}

    //根據id獲取兑换列表
    public function getPrizeTypeListToId($id){
        $rs = Yii::app()->db->createCommand()->select("*")->from("cy_prize_type")->where("id=:id",array(":id"=>$id))->queryRow();
        if($rs){
            return $rs;
        }
        return array();
    }

	//兑换列表
	public function getPrizeTypeAll() {
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("cy_prize_type")->queryAll();
        $arr = array();
		if ($rows) {
		    foreach ($rows as $row){
                $arr[$row["id"]] = $row["prize_name"];
            }
		}
		return $arr;
	}

    //獲取獎勵類型列表
    public function getPrizeTypeList(){
        $arr = array(
            ""=>array("name"=>"","num"=>"")
        );
        $rs = Yii::app()->db->createCommand()->select()->from("cy_prize_type")->order("z_index desc")->queryAll();
        if($rs){
            foreach ($rs as $row){
                $arr[$row["id"]] =array("name"=>$row["prize_name"],"num"=>$row["prize_point"]);
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
            $this->updateDocman($connection,$this->docType);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

    protected function updateDocman(&$connection, $doctype) {
        if ($this->scenario=='new') {
            $docidx = strtolower($doctype);
            if ($this->docMasterId[$docidx] > 0) {
                $docman = new DocMan($doctype,$this->id,get_class($this));
                $docman->masterId = $this->docMasterId[$docidx];
                $docman->updateDocId($connection, $this->docMasterId[$docidx]);
            }
            $this->scenario = "edit";
        }
    }

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from cy_prize_type where id = :id";
                break;
            case 'new':
                $sql = "insert into cy_prize_type(
							prize_name, prize_point, z_index, prize_remark, lcu
						) values (
							:prize_name, :prize_point, :z_index, :prize_remark, :lcu
						)";
                break;
            case 'edit':
                $sql = "update cy_prize_type set
							prize_name = :prize_name, 
							prize_point = :prize_point, 
							prize_remark = :prize_remark, 
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
        if (strpos($sql,':prize_name')!==false)
            $command->bindParam(':prize_name',$this->prize_name,PDO::PARAM_STR);
        if (strpos($sql,':prize_point')!==false)
            $command->bindParam(':prize_point',$this->prize_point,PDO::PARAM_INT);
        if (strpos($sql,':prize_remark')!==false)
            $command->bindParam(':prize_remark',$this->prize_remark,PDO::PARAM_STR);
        if (strpos($sql,':z_index')!==false)
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
        }
		return true;
	}
}
