<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class RequestPrizeForm extends CFormModel
{
	/* User Fields */
	public $id = 0;
	public $employee_id;
	public $employee_name;
	public $prize_type;
	public $prize_point;
	public $apply_date;
	public $apply_num=1;
	public $remark;
	public $prize_remark;
	public $reject_note;
	public $state = 0;
	public $city;
	public $lcu;
	public $luu;
	public $lcd;
	public $lud;


    public $no_of_attm = array(
        'rpri'=>0
    );
    public $docType = 'RPRI';
    public $docMasterId = array(
        'rpri'=>0
    );
    public $files;
    public $removeFileId = array(
        'rpri'=>0
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
            'prize_type'=>Yii::t('charity','Prize Name'),
            'prize_point'=>Yii::t('charity','Prize Point'),
			'remark'=>Yii::t('charity','Remark'),
			'prize_remark'=>Yii::t('charity','prize remark'),
            'reject_note'=>Yii::t('charity','Reject Note'),
            'city'=>Yii::t('charity','City'),
            'apply_date'=>Yii::t('charity','apply for time'),
            'apply_num'=>Yii::t('charity','apply for number'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, employee_id, employee_name, prize_type, prize_point, apply_num, apply_date, remark, reject_note, lcu, luu, lcd, lud','safe'),

			array('employee_id','required'),
            array('employee_id','validateEmployee'),
			array('prize_type,apply_num','required'),
            array('apply_num', 'numerical', 'min'=>1, 'integerOnly'=>true),
			array('prize_type','validatePrize'),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
		);
	}

	public function validatePrize($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select("*")->from("cy_prize_type")
            ->where("id=:id", array(':id'=>$this->prize_type))->queryRow();
        if ($rows){
            $this->prize_point = $rows["prize_point"];
            $creditList = $this->getCreditSumToYear($this->employee_id);
            $prizeRow = Yii::app()->db->createCommand()->select("sum(prize_point*apply_num) as prize_point")->from("cy_prize_request")
                ->where("employee_id=:employee_id and state = 1", array(':employee_id'=>$this->employee_id))->queryRow();
            $prizeNum = 0;//申請時當前用戶的總學分
            if($prizeRow){
                $prizeNum = $prizeRow["prize_point"];
            }
            $prizeNum = intval($creditList["end_num"])-intval($prizeNum);
            if($this->state == 1){
                if($prizeNum<intval($rows["prize_point"])*$this->apply_num){//判斷學分是否足夠扣除
                    $message = $this->employee_name.Yii::t("charity","available credits are").$prizeNum;
                    $this->addError($attribute,$message);
                    return false;
                }
            }
        }else{
            $message = Yii::t('charity','Prize Name'). Yii::t('charity',' Did not find');
            $this->addError($attribute,$message);
        }
    }

    //驗證當前用戶的權限
    public function validateNowUser($bool = false){
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $rs = Yii::app()->db->createCommand()->select("b.id,b.name")->from("hr$suffix.hr_binding a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.user_id ='$uid'")->queryRow();
        if($rs){
            if($bool){
                $this->employee_id = $rs["id"];
                $this->employee_name = $rs["name"];
            }
            return true; //已綁定員工
        }else{
            return false;
        }
    }

	public function validateEmployee($attribute, $params){
        $suffix = Yii::app()->params['envSuffix'];
        $this->employee_id = Yii::app()->user->staff_id();//
        $rows = Yii::app()->db->createCommand()->select("name,city")->from("hr$suffix.hr_employee")
            ->where("id=:id and staff_status=0 ", array(':id'=>$this->employee_id))->queryRow();
        if ($rows){
            $this->employee_name = $rows["name"];
            $this->city = $rows["city"];
        }else{
            $message = Yii::t('charity','Employee Name'). Yii::t('charity',' Did not find');
            $this->addError($attribute,$message);
        }
    }

    //獎金删除
	public function validateDelete(){
        $rows = Yii::app()->db->createCommand()->select()->from("cy_prize_request")
            ->where('id=:id and state in (0,2)', array(':id'=>$this->id))->queryRow();
        if ($rows){
            return true; //允許刪除
        }
        return false;
    }

    //獎金退回
	public function backPrize(){
        if(!Yii::app()->user->validFunction('ZR05')){//沒有權限
            return false;
        }
        $row = Yii::app()->db->createCommand()->select()->from("gr_prize_request")
            ->where('id=:id and state = 3', array(':id'=>$this->id))->queryRow();
        if ($row){
            $sum = $row["prize_point"];
            if(!empty($sum)){
                $sum = intval($sum);//需要扣減的總學分
                $year = date("Y",strtotime($row["apply_date"]));//申請的年份
                $creditList = Yii::app()->db->createCommand()->select("id,long_type,start_num,end_num,point_id")->from("gr_credit_point_ex")
                    ->where("employee_id=:employee_id and year=:year and end_num<start_num",array(":employee_id"=>$row["employee_id"],":year"=>$year))
                    ->order('long_type,lcu asc')->queryAll();
                $num = 0;//已經退回的學分
                if($creditList){
                    foreach ($creditList as $credit){
                        $nowNum = intval($credit["start_num"])-intval($credit["end_num"]);
                        if($nowNum<=$sum-$num){
                            $updateNum=$nowNum;
                        }else{
                            $updateNum=$sum-$num;
                        }
                        $num+=$updateNum;
                        $updateNum+=intval($credit["end_num"]);
                        Yii::app()->db->createCommand()->update('gr_credit_point_ex', array(
                            'end_num'=>$updateNum,
                        ), 'id=:id', array(':id'=>$credit["id"]));
                        if(intval($credit["long_type"]) > 1){ //需要修改5年限的學分
                            Yii::app()->db->createCommand()->update('gr_credit_point_ex', array(
                                //'start_num'=>$updateNum,//總積分不應該變
                                'end_num'=>$updateNum,
                            ), 'point_id=:point_id and year > :year', array(':point_id'=>$credit["point_id"],':year'=>$year));
                        }
                        if($num>=$sum){
                            break;
                        }
                    }
                    if($num<$sum){
                        //異常
                    }
                }else{
                    return false;
                }
            }
            Yii::app()->db->createCommand()->update('gr_prize_request', array(
                'state'=>0,
                //'reject_note'=>$this->reject_note,
            ), 'id=:id', array(':id'=>$this->id));
            return true; //允許退回
        }
        return false;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("a.*,c.prize_remark,b.name as employee_name,docman$suffix.countdoc('RPRI',a.id) as rpridoc")
            ->from("cy_prize_request a")
            ->leftJoin("cy_prize_type c","a.prize_type = c.id")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and b.city in ($city_allow) ", array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->employee_id = $row['employee_id'];
				$this->employee_name = $row['employee_name'];
                $this->prize_type = $row['prize_type'];
                $this->prize_point = $row['prize_point']*$row['apply_num'];
                $this->apply_date = $row['apply_date'];
                $this->prize_remark = $row['prize_remark'];
                $this->remark = $row['remark'];
                $this->reject_note = $row['reject_note'];
                $this->state = $row['state'];
                $this->apply_num = $row['apply_num'];
                $this->lcu = $row['lcu'];
                $this->luu = $row['luu'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
                $this->city = $row['city'];
                $this->apply_date = CGeneral::toDate($row['apply_date']);
                $this->no_of_attm['rpri'] = $row['rpridoc'];
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
            $this->updateDocman($connection,'RPRI');
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
                $sql = "delete from cy_prize_request where id = :id and city IN ($city_allow)";
				break;
			case 'new':
				$sql = "insert into cy_prize_request(
							employee_id, apply_date, apply_num, prize_type, prize_point, remark, state, city, lcu
						) values (
							:employee_id, :apply_date, :apply_num, :prize_type, :prize_point, :remark, :state, :city, :lcu
						)";
				break;
			case 'edit':
				$sql = "update cy_prize_request set
							employee_id = :employee_id, 
							apply_date = :apply_date, 
							apply_num = :apply_num, 
							prize_type = :prize_type, 
							prize_point = :prize_point,
							remark = :remark,
							reject_note = '',
							state = :state,
							luu = :luu
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':apply_date')!==false)
			$command->bindParam(':apply_date',date("Y-m-d H:i:s"),PDO::PARAM_STR);
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_INT);
		if (strpos($sql,':prize_type')!==false)
			$command->bindParam(':prize_type',$this->prize_type,PDO::PARAM_INT);
		if (strpos($sql,':apply_num')!==false)
			$command->bindParam(':apply_num',$this->apply_num,PDO::PARAM_INT);
		if (strpos($sql,':prize_point')!==false)
			$command->bindParam(':prize_point',$this->prize_point,PDO::PARAM_STR);
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
        $this->sendEmail();
        return true;
	}

    //發送郵件
    protected function sendEmail(){
        if($this->state == 1){
            $email = new Email();
            $suffix = Yii::app()->params['envSuffix'];
            $row = Yii::app()->db->createCommand()->select("a.*,c.prize_name,b.name as employee_name,b.code as employee_code,b.city as s_city")
                ->from("cy_prize_request a")
                ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
                ->leftJoin("cy_prize_type c","a.prize_type = c.id")
                ->where("a.id=:id", array(':id'=>$this->id))->queryRow();
            $description="慈善分兑换申请 - ".$row["employee_name"];
            $subject="慈善分兑换申请 - ".$row["employee_name"];
            $message="<p>员工编号：".$row["employee_code"]."</p>";
            $message.="<p>员工姓名：".$row["employee_name"]."</p>";
            $message.="<p>员工城市：".CGeneral::getCityName($row["s_city"])."</p>";
            $message.="<p>兑换名称：".$row["prize_name"]."</p>";
            $message.="<p>申请时间：".CGeneral::toDate($row["apply_date"])."</p>";
            $message.="<p>申请数量：".$row["apply_num"]."</p>";
            $message.="<p>扣減慈善分数值：".($row["prize_point"]*$row["apply_num"])."</p>";
            $email->setDescription($description);
            $email->setMessage($message);
            $email->setSubject($subject);
            $email->addEmailToPrefixAndCity("GA03",$row["s_city"]);
            $email->sent();
        }
    }

	//獲取某員工的某年度的總學分
    public function getCreditSumToYear($employee_id="",$year=""){
	    if(empty($employee_id)){
            $employee_id = Yii::app()->user->staff_id();
        }
	    if(empty($year)){
            $year = date("Y");
        }
        $rows = Yii::app()->db->createCommand()->select("sum(start_num) as start_num,sum(end_num) as end_num")->from("cy_credit_point")
            ->where("employee_id=:employee_id and year=:year", array(':employee_id'=>$employee_id,':year'=>$year))->queryRow();
	    if($rows){
	        return $rows;
        }else{
	        return array(
	            "start_num"=>0,
	            "end_num"=>0,
            );
        }
    }
}
