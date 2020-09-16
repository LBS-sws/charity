<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class SearchCreditForm extends CFormModel
{
    /* User Fields */
    public $id = 0;
    public $employee_id;
    public $employee_name;
    public $credit_type;
    public $credit_point;
    public $images_url;
    public $apply_date;
    public $remark;
    public $reject_note;
    public $state = 0;
    public $city;
    public $lcu;
    public $luu;
    public $lcd;
    public $lud;
    public $rule;
    public $review_str;
    public $position;
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
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('id, employee_id, employee_name, credit_type, credit_point, apply_date, images_url, remark, reject_note, lcu, luu, lcd, lud','safe'),

            array('id','required'),
            array('id','validateId'),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
        );
    }

    public function validateId($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select("*")->from("cy_credit_point")
            ->where("request_id=:id and start_num = end_num", array(':id'=>$this->id))->queryAll();
        if ($rows){
            $long_type = $rows[0]["long_type"];
            if(count($rows)!=$long_type){
                $message = Yii::t('charity',"The charity has been redeemed and cannot be returned");
                $this->addError($attribute,$message);
            }
        }else{
            $message = Yii::t('charity',"The charity has been redeemed and cannot be returned");
            $this->addError($attribute,$message);
        }
    }

    public function retrieveData($index)
    {
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $citySql = " b.city IN ($city_allow) ";
        if(Yii::app()->user->validFunction('ZR03')){
            $citySql = " a.id>0 ";
        }
        $rows = Yii::app()->db->createCommand()->select("a.*,d.review_str,b.position,b.name as employee_name,d.rule,d.remark as s_remark,docman$suffix.countdoc('CYRAL',a.id) as cyraldoc")
            ->from("cy_credit_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("cy_credit_type d","a.credit_type = d.id")
            ->where("a.id=:id and $citySql and a.state = 3", array(':id'=>$index))->queryAll();
        if (count($rows) > 0)
        {
            foreach ($rows as $row)
            {
                $this->id = $row['id'];
                $this->employee_id = $row['employee_id'];
                $this->employee_name = $row['employee_name'];
                $this->credit_type = $row['credit_type'];
                $this->credit_point = $row['credit_point'];
                $this->apply_date = $row['apply_date'];
                $this->images_url = $row['images_url'];
                $this->remark = $row['remark'];
                $this->reject_note = $row['reject_note'];
                $this->state = $row['state'];
                $this->review_str = $row['review_str'];
                $this->lcu = $row['lcu'];
                $this->position = $row['position'];
                $this->luu = $row['luu'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
                $this->rule = $row['rule'];
                $this->city = $row['city'];
                $this->apply_date = CGeneral::toDate($row['apply_date']);
                $this->s_remark = $row['s_remark'];
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

    protected function saveGoods(&$connection) {

        //補回學分
        $this->cancelCredit();

        $sql = '';
        switch ($this->scenario) {
            case 'cancel':
                $sql = "update cy_credit_request set
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


    protected function cancelCredit() {
        //取消慈善分
        if($this->scenario == "cancel"){
            Yii::app()->db->createCommand()->delete("cy_credit_point", "request_id=:request_id",array("request_id"=>$this->id));
        }
        return true;
    }

    //判斷輸入框能否修改
    public function getInputBool(){
        return true;
    }
}
