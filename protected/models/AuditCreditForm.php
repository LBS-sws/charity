<?php

class AuditCreditForm extends CFormModel
{
    /* User Fields */
    public $id = 0;
    public $employee_id;
    public $employee_name;
    public $position;
    public $department;
    public $credit_type;
    public $credit_point;
    public $charity_name;
    public $charity_point;
    public $images_url;
    public $apply_date;
    public $remark;
    public $reject_note;
    public $state = 0;//狀態 0：草稿 1：發送  2：拒絕  3：完成  4:確定
    public $type_state=2; //1:專員審核 2：總部審核
    public $one_date; //
    public $two_date; //
    public $one_audit; //
    public $two_audit; //
    public $city;
    public $lcu;
    public $luu;
    public $lcd;
    public $lud;
    public $rule;
    public $review_str;
    public $validity;
    public $s_remark;

    public $no_of_attm = array(
        'cyral'=>0
    );
    public $docType = 'CYRAL';
    public $docMasterId = array(
        'cyral'=>0
    );
    public $files;
    public $removeFileId = array(
        'cyral'=>0
    );
    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('charity','Record ID'),
            'employee_id'=>Yii::t('charity','Employee Name'),
            'employee_name'=>Yii::t('charity','Employee Name'),
            'credit_type'=>Yii::t('charity','Charity Name'),
            'credit_point'=>Yii::t('charity','Charity Num'),
            'charity_name'=>Yii::t('charity','Charity Name'),
            'charity_point'=>Yii::t('charity','Charity Num'),
            'rule'=>Yii::t('charity','conditions'),
            'remark'=>Yii::t('charity','Remark'),
            's_remark'=>Yii::t('charity','Charity Remark'),
            'reject_note'=>Yii::t('charity','Reject Note'),
            'city'=>Yii::t('charity','City'),
            'apply_date'=>Yii::t('charity','apply for time'),
            'review_str'=>Yii::t('charity','Review timer number'),
            'one_date'=>Yii::t('charity','one date'),
            'two_date'=>Yii::t('charity','two date'),
            'one_audit'=>Yii::t('charity','one audit'),
            'two_audit'=>Yii::t('charity','two audit'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('id, employee_id, employee_name, s_remark, credit_type, rule, review_str, credit_point, city, validity, apply_date, images_url, remark, reject_note, lcu, luu, lcd, lud','safe'),

            array('reject_note','required',"on"=>"reject"),
            array('id','required'),
            array('id','validateId'),
        );
    }

    public function validateId($attribute, $params){
        $type = key_exists("type",$_GET)?$_GET["type"]:2;
        $type = in_array($type,array(1,2))?$type:2;
        $rows = Yii::app()->db->createCommand()->select("*")->from("cy_credit_request")
            ->where("id=:id and state = 1 and type_state=$type", array(':id'=>$this->id))->queryRow();
        if ($rows){
            $this->type_state = $rows["type_state"];
            $this->employee_id = $rows["employee_id"];
            $this->apply_date = $rows["apply_date"];
            $this->credit_point = $rows["credit_point"];
        }else{
            $message = Yii::t('charity','Charity Name'). Yii::t('charity',' Did not find');
            $this->addError($attribute,$message);
        }
    }


    public function retrieveData($index,$type=2) {
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.*,d.remark as s_remark,d.validity,b.department,b.position,b.name as employee_name,b.city as s_city,d.review_str,d.rule,docman$suffix.countdoc('CYRAL',a.id) as cyraldoc")
            ->from("cy_credit_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("cy_credit_type d","a.credit_type = d.id")
            ->where("a.id=:id and a.state = 1 and b.city in ($city_allow) and a.type_state=$type", array(':id'=>$index))->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->employee_id = $row['employee_id'];
                $this->employee_name = $row['employee_name'];
                $this->department = $row['department'];
                $this->type_state = $row['type_state'];
                $this->position = $row['position'];
                $this->credit_type = $row['credit_type'];
                $this->credit_point = $row['credit_point'];
                $this->apply_date = $row['apply_date'];
                $this->images_url = $row['images_url'];
                $this->remark = $row['remark'];
                $this->s_remark = $row['s_remark'];
                $this->reject_note = $row['reject_note'];
                $this->state = $row['state'];
                $this->review_str = $row['review_str'];
                $this->one_date = $row['one_date'];
                $this->two_date = $row['two_date'];
                $this->one_audit = $row['one_audit'];
                $this->two_audit = $row['two_audit'];
                $this->rule = $row['rule'];
                $this->lcu = $row['lcu'];
                $this->luu = $row['luu'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
                $this->city = $row['s_city'];
                $this->validity = $row['validity'];
                $this->apply_date = CGeneral::toDate($row['apply_date']);
                $this->no_of_attm['cyral'] = $row['cyraldoc'];
                break;
            }
        }
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

    /*  id;employee_id;employee_code;employee_name;reward_id;reward_name;reward_money;reward_goods;remark;city;*/
	protected function saveGoods(&$connection) {
        //添加學分及積分
        if($this->scenario == "audit"&&$this->type_state==2){
            $this->auditCredit();
        }

		$sql = '';
        switch ($this->scenario) {
            case 'audit':
                $sql = "update cy_credit_request set
							state = 3, 
							luu = :luu
						where id = :id
						";
                break;
            case 'reject':
                $sql = "update cy_credit_request set
							state = 2, 
							reject_note = :reject_note, 
							luu = :luu
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':reject_note')!==false)
            $command->bindParam(':reject_note',$this->reject_note,PDO::PARAM_STR);

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();

        $this->sendEmail();
        $this->updateType();
		return true;
	}

    //專員轉化成老總考核
	protected function updateType(){
        $date = date("Y-m-d H:i:s");
        $display_name = Yii::app()->user->user_display_name();
        switch ($this->type_state){
            case 1://專員
                Yii::app()->db->createCommand()->update('cy_credit_request', array(
                    'one_audit'=>$display_name,
                    'one_date'=>$date,
                    'state'=>$this->getScenario()=="reject"?2:1,
                    'type_state'=>2,
                ), 'id=:id', array(':id'=>$this->id));
                break;
            case 2://老總
                Yii::app()->db->createCommand()->update('cy_credit_request', array(
                    'two_audit'=>$display_name,
                    'two_date'=>$date,
                ), 'id=:id', array(':id'=>$this->id));
                break;
        }
    }

    //發送郵件
    protected function sendEmail(){
        $email = new Email();
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name,b.code as employee_code,b.city as s_city")
            ->from("cy_credit_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id", array(':id'=>$this->id))->queryRow();
        if($this->scenario == "audit"){
            if($this->type_state==1){
                $str = "慈善分申请";
                $email->addEmailToPrefixAndCity("GA01",$row["s_city"]);
            }else{
                $str = "慈善分审核通过";
                $email->addEmailToStaffId($row["employee_id"]);
            }
        }else{
            $str = "慈善分被拒绝";
            $email->addEmailToStaffId($row["employee_id"]);
        }
        $description="$str - ".$row["employee_name"];
        $subject="$str - ".$row["employee_name"];
        $message="<p>员工编号：".$row["employee_code"]."</p>";
        $message.="<p>员工姓名：".$row["employee_name"]."</p>";
        $message.="<p>员工城市：".CGeneral::getCityName($row["s_city"])."</p>";
        $message.="<p>申请时间：".CGeneral::toDate($row["apply_date"])."</p>";
        $message.="<p>慈善分数值：".$row["credit_point"]."</p>";
        if($this->scenario != "audit"){
            $message.="<p>拒绝原因：".$row["reject_note"]."</p>";
        }
        $email->setDescription($description);
        $email->setMessage($message);
        $email->setSubject($subject);
        $email->sent();
    }

    //判斷輸入框能否修改
    public function getInputBool(){
        return true;
    }

    //審核通過后添加學分及積分
    private function auditCredit(){
        $remark = $this->remark;
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("a.*,d.validity,b.name as employee_name,b.city as s_city")
            ->from("cy_credit_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("cy_credit_type d","a.credit_type = d.id")
            ->where("a.id=:id and a.state = 1 and b.city in ($city_allow) ", array(':id'=>$this->id))->queryRow();
        if($row){
            $startDate = $row["apply_date"];
            $year = intval(date("Y",strtotime($startDate)));
            $validity = intval($row["validity"]);
            $row["city"] = $row["s_city"];
            //學分年度記錄
            if(is_numeric($validity)){
                for ($i = 0;$i<$validity;$i++){
                    Yii::app()->db->createCommand()->insert('cy_credit_point', array(
                        'employee_id'=>$this->employee_id,
                        'request_id'=>$this->id,
                        'long_type'=>$validity,
                        'year'=>$i+$year,
                        'start_num'=>$this->credit_point,
                        'end_num'=>$this->credit_point,
                        'lcu'=>$row["lcu"]
                    ));
                }
            }
        }else{
            throw new CHttpException(404,'Cannot update.222');
            return false;
        }
    }
}
