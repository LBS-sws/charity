<?php


class VacationDayForm
{
    public $employee_id;
    public $vacation_id;
    public $employee_list;
    public $vacation_list;//後期修改，不循環查詢（2019-10-28）
    public $time;
    public $vaca_type;
    public $diffMonth=0;
    public $remain_bool=false;//该假期类型是否有规则

    protected $vacation_sum=0;//剩餘天數
    protected $sumDay=0;//累計天數
    protected $useDay=0;//已使用天數
    protected $vacation_id_list=array();
    protected $start_time;
    protected $end_time;

    protected $year_type = "E";//年假

    protected $error_bool = false;

    public function __construct($employee_id='',$vacation_id='E',$time='')
    {
        $time = empty($time)?date("Y/m/d"):$time;
        $this->employee_id = $employee_id;
        $this->vacation_id = $vacation_id;
        $this->time = $time;
        $this->init();
    }

    public function init(){
        if(!empty($this->employee_id)){
            $this->setEmployeeList($this->employee_id);
        }
        $this->setVacationId($this->vacation_id);
    }

    public function setEmployeeList($employee_id){
        $this->employee_id = $employee_id;
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("year_day,city,entry_time")->from("hr$suffix.hr_employee")
            ->where('id=:id',array(':id'=>$employee_id))->queryRow();
        if($rows){
            $this->employee_list = $rows;
            $this->diffMonth = -1;
            $this->remain_bool = false;
            $this->error_bool = false;
            $this->sumDay = 0;
            $this->useDay = 0;
        }else{
            $this->error_bool = true;
        }
    }

    public function getErrorBool(){
        return $this->error_bool;
    }

    public function setVacationId($vacation_id){
        $this->vacation_list = '';
        $this->vacation_id_list = array();
        if ($vacation_id === $this->year_type){ //特別處理年假
            $suffix = Yii::app()->params['envSuffix'];
            $row = Yii::app()->db->createCommand()->select("*")->from("hr$suffix.hr_vacation")
                ->where("vaca_type=:vaca_type",array(":vaca_type"=>$this->year_type))->queryRow();
            if($row){
                $this->vacation_id = $row["id"];
                $this->vacation_list = $row;
                if($row['ass_bool'] == 1){ //有關聯假期規則
                    $this->vacation_id_list = explode(",",$row['ass_id']);
                }
                $this->vacation_id_list[] = $row["id"];
            }else{
                $this->error_bool = true;
            }
        }
        $this->vacation_sum = 0;
        $this->remain_bool = false;
        $this->error_bool = false;
        $this->sumDay = 0;
        $this->useDay = 0;
    }

    //計算相隔多少月份
    private function diffBetweenToMonth(){
        if($this->employee_list){
            $entry_time = strtotime($this->employee_list["entry_time"]);
            $time = strtotime($this->time);
            if($entry_time<$time){
                $year = date("Y",$time);
                $diffYear = date("Y",$time)-date("Y",$entry_time);
                $diffMonth = date("m",$time)-date("m",$entry_time);
                $diffDay = date("d",$time)-date("d",$entry_time);
                if($diffYear>0){
                    $diffMonth +=($diffYear*12);
                }
                if($diffDay<0){
                    $diffMonth--;
                }
                $this->diffMonth = $diffMonth;
                if(date("m-d",$time)>=date("m-d",$entry_time)){
                    $this->start_time = $year.date("/m/d",$entry_time);
                    $this->end_time = (intval($year)+1).date("/m/d",$entry_time);
                }else{
                    $this->start_time = (intval($year)-1).date("/m/d",$entry_time);
                    $this->end_time = $year.date("/m/d",$entry_time);
                }
            }else{
                $this->diffMonth = 0;
                $this->start_time = '';
                $this->end_time = '';
            }
        }
    }

    public function getEndTime(){
        return $this->end_time;
    }

    public function getVacationSum($lcd=''){
        $this->diffBetweenToMonth();//計算時間段
        $this->foreachVacationSum($this->vacation_id);//計算總假期天數
        $this->sumDay=$this->vacation_sum;
        $this->foreachVacationUse($lcd);//減去已申請的假期

        return $this->vacation_sum;
    }

    //計算已申請多少假期
    private function foreachVacationUse($lcd=''){
        if(!$this->employee_list){
            return false;
        }
        $vacation_id_list = implode(",",$this->vacation_id_list);
        if(empty($lcd)){
            $statusSql = " and status NOT IN (0,3)";
        }else{
            $lcd = date("Y/m/d",strtotime($lcd));
            $statusSql = " and status =  4 and date_format(lcd,'%Y/%m/%d')<='$lcd'";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $sum = Yii::app()->db->createCommand()->select("sum(log_time)")->from("hr$suffix.hr_employee_leave")
            ->where("employee_id=:employee_id and vacation_id in ($vacation_id_list) $statusSql and date_format(start_time,'%Y/%m/%d')>:start_time and date_format(start_time,'%Y/%m/%d')<:end_time",
                array(":employee_id"=>$this->employee_id,":start_time"=>$this->start_time,":end_time"=>$this->end_time))->queryScalar();

        $this->vacation_sum-=$sum;
        $this->useDay = $sum;
    }

    public function getSumDay(){
        return $this->sumDay;
    }

    public function getUseDay(){
        return $this->useDay;
    }


    //計算在假期規則裡是多少天假期
    private function foreachVacationSum($vacation_id){
        if ($this->error_bool){
            return false;
        }
        $suffix = Yii::app()->params['envSuffix'];
        if(empty($this->vacation_list)){
            $row = Yii::app()->db->createCommand()->select("*")->from("hr$suffix.hr_vacation")
                ->where('id=:id',array(':id'=>$vacation_id))->queryRow();
            if($row['ass_bool'] == 1){ //有關聯假期規則
                $this->vacation_id_list = explode(",",$row['ass_id']);
            }
            $this->vacation_id_list[] = $row["id"];
        }else{
            $row = $this->vacation_list;
        }
        if($row){
            $yearLeave = Yii::app()->params['yearLeave'];
            var_dump($yearLeave);
            if($row["vaca_type"]==$this->year_type&&$yearLeave === "employee"){
                $this->remain_bool = true;
                $this->addEmployeeNum();//年假根據員工信息計算
            }else{
                $this->addRulesNum($row);//假期規則添加天數
            }
            $this->addYearLeaveNum($row);//根據假期種類，分別對待
        }else{
            $this->error_bool = true;
        }
    }

    //累計年假
    private function addEmployeeNum(){
        if($this->employee_list){
            $this->vacation_sum=$this->employee_list["year_day"];
        }
    }

    private function addRulesNum($row){
        if($row['log_bool'] == 1){//有假期規則
            $this->remain_bool = true;
            $max_log = json_decode($row['max_log'],true);
            foreach ($max_log as $list){
                if ($this->diffMonth<$list["monthLong"]){
                    if($this->vacation_sum<$list["dayNum"]){
                        $this->vacation_sum=$list["dayNum"];
                    }
                    break;
                }elseif($list["monthLong"]==="other"){
                    if($this->vacation_sum<$list["dayNum"]){
                        $this->vacation_sum=$list["dayNum"];
                    }
                    break;
                }
            }
        }
    }

    //累計年假
    private function addYearLeaveNum($vacation_list){
        switch ($vacation_list["vaca_type"]){
            case $this->year_type://年假（需要添加累計年假的天數）
                $year = date("Y",strtotime($this->start_time));
                $suffix = Yii::app()->params['envSuffix'];
                $sum = Yii::app()->db->createCommand()->select("sum(add_num)")->from("hr$suffix.hr_staff_year")
                    ->where("employee_id=:employee_id and year=:year",array(":employee_id"=>$this->employee_id,":year"=>$year))->queryScalar();
                $this->vacation_sum+=$sum;
                break;
        }
    }

    public function setTime($time){
        $this->time = $time;
    }

    public function getEmployeeList(){
        return $this->employee_list;
    }

    private function LeaveTime($entry_time,$time){
        $year = date("Y",strtotime($time));
        $month = date("m/d",strtotime($time));
        $entry_time = date("m/d",strtotime($entry_time));
        if($entry_time>$month){
            $year--;
        }

        return array(
            "minDay"=>$year."/".$entry_time,
            "maxDay"=>($year+1)."/".$entry_time,
        );
    }
}
