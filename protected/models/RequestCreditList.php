<?php

class RequestCreditList extends CListPageModel
{
    public $searchTimeStart;//開始日期
    public $searchTimeEnd;//結束日期
    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('charity','ID'),
            'employee_id'=>Yii::t('charity','Employee Name'),
            'employee_name'=>Yii::t('charity','Employee Name'),
            'credit_type'=>Yii::t('charity','Charity Name'),
            'charity_name'=>Yii::t('charity','Charity Name'),
            'credit_point'=>Yii::t('charity','Charity Num'),
            'city'=>Yii::t('charity','City'),
            'city_name'=>Yii::t('charity','City'),
            'state'=>Yii::t('charity','Status'),//狀態 0：草稿 1：發送  2：拒絕  3：完成  4:確定
            'apply_date'=>Yii::t('charity','apply for time'),
            'category'=>Yii::t('charity','integral type'),
            'exp_date'=>Yii::t('charity','expiration date'),
        );
    }

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, searchTimeStart, searchTimeEnd','safe',),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
        $staffId = Yii::app()->user->staff_id();//
        if(Yii::app()->user->validRWFunction("GA01")||Yii::app()->user->validRWFunction("GA03")){
            $whereSql = "a.id>0 and d.city in ($city_allow)";
        }else{
            $whereSql = "a.id>0 and (a.lcu='$uid' or a.employee_id='$staffId')";
        }
        $sql1 = "select a.*,b.charity_name,d.name AS employee_name,d.city AS s_city from cy_credit_request a
                LEFT JOIN cy_credit_type b ON a.credit_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where $whereSql 
			";
        $sql2 = "select count(a.id) from cy_credit_request a
                LEFT JOIN cy_credit_type b ON a.credit_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where $whereSql 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('d.name',$svalue);
                    break;
                case 'charity_name':
                    $clause .= General::getSqlConditionClause('b.charity_name',$svalue);
                    break;
                case 'credit_point':
                    $clause .= General::getSqlConditionClause('a.credit_point',$svalue);
                    break;
                case 'city_name':
                    $clause .= ' and d.city in '.$this->getCityCodeSqlLikeName($svalue);
                    break;
                case 'state':
                    $clause .= $this->getStatusSqlLikeName(' and a.state in ',$svalue);
                    break;
            }
        }
        if (!empty($this->searchTimeStart) && !empty($this->searchTimeStart)) {
            $svalue = str_replace("'","\'",$this->searchTimeStart);
            $clause .= " and date_format(a.apply_date,'%Y/%m/%d') >='$svalue' ";
        }
        if (!empty($this->searchTimeEnd) && !empty($this->searchTimeEnd)) {
            $svalue = str_replace("'","\'",$this->searchTimeEnd);
            $clause .= " and date_format(a.apply_date,'%Y/%m/%d') <='$svalue' ";
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        } else
            $order = " order by a.id desc";

        $sql = $sql2.$clause;
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = $sql1.$clause.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                $colorList = $this->statusToColor($record['state'],$record['lcd']);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'employee_name'=>$record['employee_name'],
                    'charity_name'=>$record['charity_name'],
                    'credit_point'=>$record['credit_point'],
                    'apply_date'=>date("Y-m-d",strtotime($record['apply_date'])),
                    'exp_date'=>date("Y-12-31",strtotime($record['apply_date']." + 4 year")),
                    'status'=>$colorList["status"],
                    'city'=>CGeneral::getCityName($record["s_city"]),
                    'style'=>$colorList["style"],
                );
            }
        }
        $session = Yii::app()->session;
        $session['requestCredit_op01'] = $this->getCriteria();
        return true;
    }

    //根據狀態獲取顏色
    public function statusToColor($status,$lcd){
        switch ($status){
            // text-danger
            case 0:
                return array(
                    "status"=>Yii::t("charity","Draft"),//草稿
                    "style"=>""
                );
            case 1:
                return array(
                    "status"=>Yii::t("charity","Confirmed, pending review"),//已确认，等待審核
                    "style"=>" text-primary"
                );
            case 2:
                return array(
                    "status"=>Yii::t("charity","Rejected"),//拒絕
                    "style"=>" text-danger"
                );
            case 3:
                return array(
                    "status"=>Yii::t("charity","approve"),//批准
                    "style"=>" text-green"
                );
        }
        return array(
            "status"=>"",
            "style"=>""
        );
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

//获取地区編號（模糊查詢）
    public function getStatusSqlLikeName($sql,$name){
        $statusList=array(
            4=>Yii::t("integral","Confirmed, pending review"),
            3=>Yii::t("integral","approve"),
            2=>Yii::t("integral","Rejected"),
            1=>Yii::t("integral","Sent, to be confirmed"),
            0=>Yii::t("integral","Draft"),
        );
        $arr = array();
        foreach ($statusList as $key=>$value){
            if (strpos($value,$name)!==false)
                $arr[] = "'".$key."'";
        }
        if(empty($arr)){
            return "";
        }else{
            $arr = implode(",",$arr);
            return $sql."($arr)";
        }
    }
}
