<?php
/* Reimbursement Form */

class ReportY02Form extends CReportForm
{
	public $staffs;
	public $staffs_desc;
    public $city_desc;
    public $city_allow;
    public $auth_bool=0; //0:沒有所有地區權限，1：所有地區

	protected function labelsEx() {
		return array(
				'staffs'=>Yii::t('charity','Staffs'),
			);
	}
	
	protected function rulesEx() {
        return array(
            array('city,staffs,start_dt,end_dt, staffs_desc, auth_bool','safe'),
        );
	}
	
	protected function queueItemEx() {
		return array(
            'CITY'=>$this->city,
            'STAFFS'=>$this->staffs,
            'STAFFSDESC'=>$this->staffs_desc,
            'city_allow'=>$this->city_allow,
            'auth_bool'=>$this->auth_bool,
            'CITYDESC'=>$this->city_desc,
        );
	}
	
	public function init() {
		$this->id = 'RptCreditsList';
		$this->name = Yii::t('app','Credits subsidiary List');
		$this->format = 'EXCEL';
		$this->fields = 'city,start_dt,end_dt,staffs,staffs_desc';
        $this->start_dt = date("Y/01/01");
        $this->end_dt = date("Y/12/31");
		$this->city = Yii::app()->user->city();
		$this->staffs = '';
		$this->staffs_desc = Yii::t('misc','All');
        $this->city = '';
        $this->city_desc = Yii::t('misc','All');
	}

}
