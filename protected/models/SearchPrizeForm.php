<?php

class SearchPrizeForm extends CFormModel
{
    /* User Fields */
    public $id = 0;
    public $employee_id;
    public $employee_name;
    public $prize_type;
    public $prize_name;
    public $prize_point;
    public $apply_date;
    public $apply_num;
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
            'prize_name'=>Yii::t('charity','Prize Name'),
            'prize_type'=>Yii::t('charity','Prize Name'),
            'prize_remark'=>Yii::t('charity','prize remark'),
            'prize_point'=>Yii::t('charity','Prize Point'),
            'remark'=>Yii::t('charity','Remark'),
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
            array('id, employee_id, employee_name, apply_num, prize_remark, prize_name, prize_type, credit_type, credit_point, city, validity, apply_date, images_url, remark, reject_note, lcu, luu, lcd, lud','safe'),

            array('id','required'),
            array('id','validateId'),
        );
    }

    public function validateId($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select("*")->from("cy_prize_request")
            ->where("id=:id and state = 3", array(':id'=>$this->id))->queryRow();
        if ($rows){
            $this->employee_id = $rows["employee_id"];
            $this->apply_date = $rows["apply_date"];
            $this->apply_num = $rows["apply_num"];
            $this->prize_point = $rows["prize_point"];
        }else{
            $message = Yii::t('charity','Prize Name'). Yii::t('charity',' Did not find');
            $this->addError($attribute,$message);
        }
    }

    public function retrieveData($index)
    {
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        if(Yii::app()->user->validFunction('ZR03')){ //查詢所有員工
            $citySql = " a.id>0 ";
        }else{
            $citySql = " b.city IN ($city_allow) ";
        }
        $rows = Yii::app()->db->createCommand()->select("a.*,c.prize_name,c.prize_remark,b.name as employee_name")
            ->from("cy_prize_request a")
            ->leftJoin("cy_prize_type c","a.prize_type = c.id")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and $citySql ", array(':id'=>$index))->queryAll();
        if (count($rows) > 0)
        {
            foreach ($rows as $row)
            {
                $this->id = $row['id'];
                $this->employee_id = $row['employee_id'];
                $this->employee_name = $row['employee_name'];
                $this->prize_remark = $row['prize_remark'];
                $this->prize_name = $row['prize_name'];
                $this->prize_type = $row['prize_type'];
                $this->prize_point = $row['prize_point']*$row['apply_num'];
                $this->apply_num = $row['apply_num'];
                $this->remark = $row['remark'];
                $this->reject_note = $row['reject_note'];
                $this->state = $row['state'];
                $this->lcu = $row['lcu'];
                $this->luu = $row['luu'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
                $this->city = $row['city'];
                $this->apply_date = CGeneral::toDate($row['apply_date']);
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

    protected function saveGoods(&$connection) {

        //補回學分
        $this->cancelPrize();

        $sql = '';
        switch ($this->scenario) {
            case 'cancel':
                $sql = "update cy_prize_request set
							state = 0, 
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

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();

        return true;
    }


    protected function cancelPrize() {
        //取消兌換
        if($this->scenario == "cancel"){
            $sum = $this->prize_point*$this->apply_num;
            if(!empty($sum)){
                $sum = intval($sum);//需要補回的總學分
                $year = date("Y",strtotime($this->apply_date));//申請的年份
                $creditList = Yii::app()->db->createCommand()->select("a.id,a.long_type,a.start_num,a.end_num,a.request_id")->from("cy_credit_point a")
                    ->leftJoin('cy_credit_request b',"a.request_id = b.id")
                    ->where("a.employee_id=:employee_id and a.year=:year and a.end_num != a.start_num",array(":employee_id"=>$this->employee_id,":year"=>$year))
                    ->order('b.apply_date ASC')->queryAll();
                $num = 0;//已經補回的學分
                if($creditList){
                    foreach ($creditList as $credit){
                        $nowNum = intval($credit["start_num"])-intval($credit["end_num"]);
                        $num+=$nowNum;
                        $updateNum = $num>$sum?$sum-$num+$nowNum:$nowNum;

                        $sql = "update cy_credit_point set end_num=end_num+$updateNum where request_id=".$credit["request_id"]." and year >= $year";
                        Yii::app()->db->createCommand($sql)->execute();
                        if($num>=$sum){
                            break;
                        }
                    }
                }else{
                    throw new CHttpException(404,'Cannot update.33333');
                }
            }
        }
        return true;
    }

    //判斷輸入框能否修改
    public function getInputBool(){
        return true;
    }

}
