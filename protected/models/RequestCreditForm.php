<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class RequestCreditForm extends CFormModel
{
	/* User Fields */
	public $id = 0;
	public $employee_id;
	public $employee_name;
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
    public $rule;
    public $review_str;
	public $lcu;
	public $luu;
	public $lcd;
	public $lud;
	public $position;
	public $department;


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
			array('id, employee_id, employee_name, credit_type,rule,review_str, credit_point,charity_name, charity_point, apply_date, images_url, rule, remark, reject_note, lcu, luu, lcd, lud','safe'),

			array('apply_date','required'),
			array('employee_id','required'),
            array('employee_id','validateEmployee'),
            array('apply_date','validateApplyDate'),
			array('credit_type','required'),
			array('credit_type','validateCharity'),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
		);
	}

	public function validateCharity($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select("*")->from("cy_credit_type")
            ->where("id=:id and (bumen=''or FIND_IN_SET(:bumen,bumen))", array(':id'=>$this->credit_type,':bumen'=>$this->department))->queryRow();
        if ($rows){
            if($rows["year_sw"]==1){
                $year = date("Y");
                $count = Yii::app()->db->createCommand()->select("count(*)")->from("cy_credit_request")
                    ->where("credit_type=:credit_type and employee_id=:employee_id and state in (1,3,4) and date_format(apply_date,'%Y') ='$year'",
                        array(':credit_type'=>$this->credit_type,':employee_id'=>$this->employee_id))->queryScalar();
                if($count >= $rows["year_max"]){
                    $message = "该学分每年申請次數不能大于".$rows["year_max"];
                    $this->addError($attribute,$message);
                }
            }else{
                $this->credit_point = $rows["charity_point"];
                $this->rule = $rows["rule"];
            }
        }else{
            $message = Yii::t('charity','Charity Name'). Yii::t('charity',' Did not find');
            $this->addError($attribute,$message);
        }
    }

	public function validateEmployee($attribute, $params){
        $rows= Yii::app()->user->staff_list();//
        if (!empty($rows)){
            $this->employee_id = $rows["id"];
            $this->position = $rows["position"];
            $this->department = $rows["department"];
            $this->city = $rows["city"];
        }else{
            $message = Yii::t('charity','Employee Name'). Yii::t('charity',' Did not find');
            $this->addError($attribute,$message);
        }
    }

	public function validateApplyDate($attribute, $params){
	    if(!empty($this->apply_date)){
            $date = date("Y-m-d");
            $thisDate = date("Y-m-d",strtotime($this->apply_date));
            if ($thisDate>$date){
                $message = Yii::t('charity','apply time shall not exceed the date of the today');
                $this->addError($attribute,$message);
            }
        }
    }

    //學分取消驗證
	public function validateCancel(){
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("a.id,a.credit_point,a.employee_id,a.employee_id,a.rec_date")->from("gr_credit_point a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.credit_req_id=:id and b.city in ($city_allow)", array(':id'=>$this->id))->queryRow();
        if ($row) {
            //學分關聯的積分是否使用
            $listArrIntegral = GiftList::getNowIntegral($row["employee_id"],$row["rec_date"]);
            if(floatval($listArrIntegral["cut"]) < floatval($row["credit_point"])){
                return array(
                    "status"=>false,
                    "message"=>Yii::t("charity","The credits associated with this credit have been used and cannot be cancelled")
                );
            }

            //學分關聯的獎金是否使用
            $rows = Yii::app()->db->createCommand()->select("*")->from("gr_credit_point_ex")
                ->where("point_id=:id", array(':id'=>$row["id"]))->order("year asc")->queryRow();
            if($rows["start_num"] != $rows["end_num"]){
                return array(
                    "status"=>false,
                    "message"=>Yii::t("charity","The credit has been used and cannot be cancelled")
                );
            }

            //學分取消
            Yii::app()->db->createCommand()->delete('gr_credit_request', 'id=:id', array(':id'=>$this->id));//刪除學分申請表
            Yii::app()->db->createCommand()->delete('gr_bonus_point', 'req_id=:id', array(':id'=>$this->id));//刪除積分表
            Yii::app()->db->createCommand()->delete('gr_credit_point', 'credit_req_id=:id', array(':id'=>$this->id));//刪除學分記錄表
            Yii::app()->db->createCommand()->delete('gr_credit_point_ex', 'point_id=:id', array(':id'=>$row["id"]));//刪除學分查詢表

            return array(
                "status"=>true
            );
        }else{
            return array(
                "status"=>false,
                "message"=>Yii::t("charity","Credit does not exist")
            );
        }
    }

    //积分删除
	public function validateDelete(){
        $rows = Yii::app()->db->createCommand()->select()->from("cy_credit_request")
            ->where('id=:id and state in (0,2)', array(':id'=>$this->id))->queryRow();
        if ($rows){
            return true; //允許刪除
        }
        return false;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        //$city_allow = Yii::app()->user->city_allow();
        $city_allow = Yii::app()->user->getEmployeeCityAll();
        $uid = Yii::app()->user->id;
        $staffId = Yii::app()->user->staff_id();//
        $rows = Yii::app()->db->createCommand()->select("a.*,d.review_str,b.department,b.position,b.name as employee_name,d.rule,d.remark as s_remark,docman$suffix.countdoc('CYRAL',a.id) as cyraldoc")
            ->from("cy_credit_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("cy_credit_type d","a.credit_type = d.id")
            ->where("a.id=:id and b.city in ($city_allow) ", array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->employee_id = $row['employee_id'];
				$this->employee_name = $row['employee_name'];
				$this->position = $row['position'];
				$this->department = $row['department'];
                $this->credit_type = $row['credit_type'];
                $this->credit_point = $row['credit_point'];
                $this->apply_date = $row['apply_date'];
                $this->images_url = $row['images_url'];
                $this->remark = $row['remark'];
                $this->reject_note = $row['reject_note'];
                $this->state = $row['state'];
                $this->review_str = $row['review_str'];
                $this->one_date = $row['one_date'];
                $this->two_date = $row['two_date'];
                $this->one_audit = $row['one_audit'];
                $this->two_audit = $row['two_audit'];
                $this->lcu = $row['lcu'];
                $this->rule = $row['rule'];
                $this->luu = $row['luu'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
                $this->city = $row['city'];
                $this->apply_date = CGeneral::toDate($row['apply_date']);
                //$this->s_remark = $row['s_remark'];
                $this->no_of_attm['cyral'] = $row['cyraldoc'];
                if($row["lcu"]!=$uid&&$row["employee_id"]!=$staffId){
                    $this->setScenario("view");
                }
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
			$this->saveStaff($connection);
            $this->updateDocman($connection,'CYRAL');
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
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

	protected function saveStaff(&$connection)
	{
		$sql = '';
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
		switch ($this->scenario) {
			case 'delete':
                $sql = "delete from cy_credit_request where id = :id and lcu=:lcu";
				break;
			case 'new':
				$sql = "insert into cy_credit_request(
							employee_id, apply_date, credit_type, credit_point, remark, type_state, state, city, lcu
						) values (
							:employee_id, :apply_date, :credit_type, :credit_point, :remark, 1, :state, :city, :lcu
						)";
				break;
			case 'edit':
				$sql = "update cy_credit_request set
							employee_id = :employee_id, 
							apply_date = :apply_date, 
							credit_type = :credit_type, 
							credit_point = :credit_point,
							remark = :remark,
							reject_note = '',
							state = :state,
							type_state = 1,
							luu = :luu
						where id = :id and lcu=:lcu
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':apply_date')!==false)
			$command->bindParam(':apply_date',$this->apply_date,PDO::PARAM_STR);
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_INT);
		if (strpos($sql,':credit_type')!==false)
			$command->bindParam(':credit_type',$this->credit_type,PDO::PARAM_INT);
		if (strpos($sql,':credit_point')!==false)
			$command->bindParam(':credit_point',$this->credit_point,PDO::PARAM_STR);
		if (strpos($sql,':remark')!==false)
			$command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
		if (strpos($sql,':state')!==false)
			$command->bindParam(':state',$this->state,PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);

		$command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
        }
        //$this->sendEmail();
        return true;
	}

    //發送郵件
    protected function sendEmail(){
        if($this->state == 1){
            $email = new Email();
            $suffix = Yii::app()->params['envSuffix'];
            $row = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name,b.code as employee_code,b.city as s_city")
                ->from("gr_credit_request a")
                ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
                ->where("a.id=:id", array(':id'=>$this->id))->queryRow();
            $description="慈善分申请 - ".$row["employee_name"];
            $subject="慈善分申请 - ".$row["employee_name"];
            $message="<p>员工编号：".$row["employee_code"]."</p>";
            $message.="<p>员工姓名：".$row["employee_name"]."</p>";
            $message.="<p>员工城市：".CGeneral::getCityName($row["s_city"])."</p>";
            $message.="<p>申请时间：".CGeneral::toDate($row["apply_date"])."</p>";
            $message.="<p>慈善分数值：".$row["credit_point"]."</p>";
            $email->setDescription($description);
            $email->setMessage($message);
            $email->setSubject($subject);
            $email->addEmailToPrefixAndCity("GA03",$row["s_city"]);
            $email->sent();
        }
    }

    //驗證當前用戶的權限
    public function validateNowUser($bool = false){
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $rs = Yii::app()->db->createCommand()->select("b.id,b.name,b.position,b.department")->from("hr$suffix.hr_binding a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.user_id ='$uid'")->queryRow();
        if($rs){
            if($bool){
                $this->employee_id = $rs["id"];
                $this->employee_name = $rs["name"];
                $this->position = $rs["position"];
                $this->department = $rs["department"];
            }
            return true; //已綁定員工
        }else{
            return false;
        }
    }

//获取地区編號（模糊查詢）
    public function getCityCodeSqlLikeName($code)
    {
        $from =  'security'.Yii::app()->params['envSuffix'].'.sec_city';
        $rows = Yii::app()->db->createCommand()->select("code")->from($from)->where(array('like', 'name', "%$code%"))->queryAll();
        $arr = array();
        foreach ($rows as $row){
            array_push($arr,"'".$row["code"]."'");
        }
        if(empty($arr)){
            return "('')";
        }else{
            $arr = implode(",",$arr);
            return "($arr)";
        }
    }
}
