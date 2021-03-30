<?php

class WebUser extends CWebUser
{
	public static function getRwFunctionList()
	{
		$id = Yii::app()->session['system'];
		return Yii::app()->session['rw_func'][$id];
	}

	public function getRoFunctionList()
	{
		$id = Yii::app()->session['system'];
		return Yii::app()->session['ro_func'][$id];
	}

	public function getCntFunctionList()
	{
		$id = Yii::app()->session['system'];
		return Yii::app()->session['cnt_func'][$id];
	}

	public function validRWFunction($func_id)
	{
		$func = $this->getRwFunctionList();
		return (strpos($func,$func_id)!==false);
	}
	
	public function validFunction($func_id) {
		$func = $this->getRwFunctionList().$this->getRoFunctionList().$this->getCntFunctionList();
		return (strpos($func,$func_id)!==false);
	}
	
	public function validSystem($sys_id) {
		$session = Yii::app()->session;
		$rw = (!isset($session['rw_func'][$sys_id])) ? false : !empty($session['rw_func'][$sys_id]);
		$ro = (!isset($session['ro_func'][$sys_id])) ? false : !empty($session['ro_func'][$sys_id]);
		$cn = (!isset($session['cnt_func'][$sys_id])) ? false : !empty($session['cnt_func'][$sys_id]);
		return ($rw || $ro || $cn);
	}

	public function system() {
		return Yii::app()->session['system'];
	}

	public function city() {
		return Yii::app()->session['city'];
	}
	
	public function city_name() {
		return Yii::app()->session['city_name'];
	}
	
	public function city_allow() {
		return Yii::app()->session['city_allow'];
	}

	public function staff_id() {
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $rs = Yii::app()->db->createCommand()->select("b.id")->from("hr$suffix.hr_binding a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.user_id ='$uid' and staff_status=0")->queryRow();
        if($rs){
            return $rs["id"];
        }else{
            return "";
        }
	}

	public function staff_list() {
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $rs = Yii::app()->db->createCommand()->select("b.id,b.name,b.code,b.city,b.position,b.department")->from("hr$suffix.hr_binding a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.user_id ='$uid' and staff_status=0")->queryRow();
        if($rs){
            return $rs;
        }else{
            return array();
        }
	}
	public function getEmployeeCityAll() {
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $rs = Yii::app()->db->createCommand()->select("b.city,b.id")->from("hr$suffix.hr_binding a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.user_id ='$uid'")->queryRow();
        if($rs){
            $cstr = $rs["city"];
            if($cstr != Yii::app()->session['city']){ //如果城市不一致，需要查臨時城市
                $row = Yii::app()->db->createCommand()->select("city,department,position")->from("hr$suffix.hr_plus_city")
                    ->where("employee_id =:employee_id",array(":employee_id"=>$rs["id"]))->queryRow();
                if($row){
                    $cstr = $row["city"];
                }
            }
            $city_allow = City::model()->getDescendantList($cstr);
            $city_allow .= (empty($city_allow)) ? "'$cstr'" : ",'$cstr'";
            return $city_allow;
        }
        return "''";
	}
	
	public function user_display_name() {
		return Yii::app()->session['disp_name'];
	}
	
	public function logon_time() {
		return Yii::app()->session['logon_time'];
	}
	
	protected function getCity() {
		return $this->city();
	}
	
	public function email() {
		return Yii::app()->session['email'];
	}

	public function employee_id() {
		return Yii::app()->session['employee_id'];
	}

	public function employee_city_all() {
		return Yii::app()->session['employee_city_all'];
	}
	
	public function isSingleCity() {
		$str = Yii::app()->session['city_allow'];
		$items = explode(",",str_replace("'","",$str));
		if (($items===false) || empty($items))
			return true;
		else
			return (count($items)<=1);
	}

    protected function setEmployeeAll(&$session){
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $rs = Yii::app()->db->createCommand()->select("b.city,b.id")->from("hr$suffix.hr_binding a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.user_id ='$uid'")->queryRow();
        if($rs){
            $cstr = $rs["city"];
            $city_allow = City::model()->getDescendantList($cstr);
            $city_allow .= (empty($city_allow)) ? "'$cstr'" : ",'$cstr'";
            $session['employee_id'] = $rs["id"];
            $session['employee_city_all'] = $city_allow;
        }else{
            $session['employee_id'] = "0";
            $session['employee_city_all'] = "''";
        }
    }
	
	protected function afterLogin($fromCookie)
	{
		$sesskey = md5(mt_rand());

		$user=User::model()->find('LOWER(username)=?',array($this->name));
		$city = City::model()->find('code=?',array($user->city));
		$city_allow = City::model()->getDescendantList($user->city);
		$cstr = $user->city;
		$city_allow .= (empty($city_allow)) ? "'$cstr'" : ",'$cstr'";
		
		$session = Yii::app()->session;
		$this->getUserOption($user);
		if (Yii::app()->params['showRank']=='on') $ranklevel = $this->getLevel($user->username);
		$access = $user->accessRights();
		if (!empty($access)) {
			$session['ro_func'] = $access['read_only'];
			$session['rw_func'] = $access['read_write'];
			$session['cnt_func'] = $access['control'];
		} else {
			$session['ro_func'] = array();
			$session['rw_func'] = array();
			$session['cnt_func'] = array();
		}
		$session['city'] = $user->city;
		$session['city_name'] = $city->name;
		$session['city_allow'] = $city_allow;
		$session['email'] = $user->email;
		$session['session_key'] = $sesskey;
		$session['session_time'] = date("Y-m-d H:i:s");
		$session['disp_name'] = $user->disp_name;
		$session['logon_time'] = $user->logon_time;
		if (Yii::app()->params['showRank']=='on') $session['ranklevel'] = $ranklevel;
		//後續添加
        //$this->setEmployeeAll($session);

		User::model()->updateByPk($this->id,
			array('logon_time'=>new CDbExpression('NOW()'),
				'session_key'=>$sesskey,
				'fail_count'=>0,
			)
		);
		
		$this->writeLoginLog();
	}

	protected function writeLoginLog() {
		$loginlog = new LoginLog;
		$loginlog->station_id = isset(Yii::app()->session['station']) ? Yii::app()->session['station'] : 'N/A';
		$loginlog->username = $this->name;
		$loginlog->client_ip = Yii::app()->request->userHostAddress;
		$loginlog->save();
	}
	
	protected function beforeLogout() {
		User::model()->updateByPk($this->id,array('logoff_time'=>new CDbExpression('NOW()'),'session_key'=>''));
		return true;
	}

	protected function getUserOption($user) {
		$options = $user->getUserOption();
		if (!empty($options)) {
			$session = Yii::app()->session;
			foreach ($options as $key=>$value) {
				switch ($key) {
					case 'lang':
						$session['lang'] = $value;
						Yii::app()->language = $value;
						break;
					case 'system':
						$session['system'] = $value;
						break;
				}
			}
		}
	}
	
	public function saveUserOption($name, $key, $value) {
		User::model()->saveUserOption($name, $key, $value);
	}

	private function getFullUrl($base, $path) {
		$pos = strpos($path, '/', 1);
		$tmp = substr($path, $pos);
		return $base.$tmp;
	}
	
	public function setUrlAfterLogin() {
		$systems = General::systemMapping();
		$baseUrl = Yii::app()->getBaseUrl(true);
		$returnUrl = $this->getFullUrl($baseUrl, $this->returnUrl);
		
		$session = Yii::app()->session;
		if (isset($session['system'])) {
			if (strpos($returnUrl,$systems[$session['system']]['webroot'])===false) {
				$found = '';
				foreach ($systems as $key=>$value) {
					if (strpos($returnUrl,$value['webroot'])!==false) {
						$found = $key;
					}
					if (($returnUrl == $value['webroot']) 
						|| ($returnUrl == $value['webroot'].'/index.php')) 
					{
						$this->returnUrl = $systems[$session['system']]['webroot'];
						$found = '';
						break;
					}
				}
				if ($found!='') $session['system'] = $found;
			}
		} else {
			$session['system'] = Yii::app()->params['systemId'];
		}
	}

	public function ranklevel() {
		return isset(Yii::app()->session['ranklevel']) ? Yii::app()->session['ranklevel'] : '';
	}
	
	protected function getLevel($uid) {
        $suffix = Yii::app()->params['envSuffix'];
		$sql = "select b.level from sales$suffix.sal_rank a, sales$suffix.sal_level b 
				where a.username='$uid' and ifnull(a.now_score,0) >= b.start_fraction and ifnull(a.now_score,0) <= b.end_fraction 
				order by a.month desc limit 1
		";
		$rtn = Yii::app()->db->createCommand($sql)->queryRow();
		return $rtn===false ? '' : $rtn['level'];
	}
}