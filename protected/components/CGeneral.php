<?php
// Common Functions

class CGeneral {
	public static function toDate($value) {
		return (empty($value) || $value==0) ? '' :
			date_format(date_create($value),"Y/m/d");
	}

	public static function toDateTime($value) {
		return (empty($value) || $value==0) ? '' :
			date_format(date_create($value),"Y/m/d H:i:s");
	}

	public static function toMyDate($value) {
		return (empty($value) || $value==0) ? null :
			date_format(date_create($value),"Y-m-d");
	}
	
	public static function toMyNumber($value) {
		return (empty($value) || $value==0 || !is_numeric($value)) ? null : $value;
	}

	public static function isDate($i_sDate) {
	/*
		function isDate
		boolean isDate(string)
		Summary: checks if a date is formatted correctly: mm/dd/yyyy (US English)
		Author: Laurence Veale (modified by Sameh Labib)
		Date: 07/30/2001
	*/
 
		$blnValid = TRUE;
   
		if ( $i_sDate == "0000/00/00" ) { return $blnValid; }
   
	// check the format first (may not be necessary as we use checkdate() below)
		if(!ereg ("^[0-9]{4}/[0-9]{2}/[0-9]{2}$", $i_sDate)) {
			$blnValid = FALSE;
		} else {
	//format is okay, check that days, months, years are okay
			$arrDate = explode("/", $i_sDate); // break up date by slash
			$intMonth = $arrDate[1];
			$intDay = $arrDate[2];
			$intYear = $arrDate[0];
 
			$intIsDate = checkdate($intMonth, $intDay, $intYear);
     
			if(!$intIsDate) {
				$blnValid = FALSE;
			}
		}//end else
   
		return ($blnValid);
	} //end function isDate

	public static function isJSON($sting) {
		call_user_func_array('json_decode',func_get_args());
		return (json_last_error()===JSON_ERROR_NONE);
	}
	
	public static function getSqlConditionClause($field, $value)
	{
		$return = '';
		if (!empty($field)){
			$val = trim($value);
			if (substr($val,0,1)=='"' && substr($val,-1)=='"') {
				$return = "and ".$field." = '" . substr(substr($val,1),0,-1) . "' ";
			} else {
				$return = "and ".$field." like '%" . $value . "%' ";
			}
		}
		return $return;
	}

	public static function getCityList()
	{
		$list = array();
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select code, name from security$suffix.sec_city order by name";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$list[$row['code']] = $row['name'];
			}
		}
		return $list;
	}

	public static function getCityListWithNoDescendant($city_allow='') {
		$list = array();
		$suffix = Yii::app()->params['envSuffix'];
		$clause = !empty($city_allow) ? "and a.code in ($city_allow)" : "";
		$sql = "select distinct a.code, a.name from security$suffix.sec_city a 
					left outer join security$suffix.sec_city b on a.code=b.region 
					where b.code is null 
					$clause 
					order by a.code
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$list[$row['code']] = $row['name'];
			}
		}
		return $list;
	}

	public static function getEmailByUserId($uid) {
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select email from security$suffix.sec_user where username='".$uid."'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		return (isset($row['email']))?$row['email']:'';
	}

	public static function getEmailByUserIdArray($uids) {
		$rtn = array();
		if (is_array($uids)) {
			foreach ($uids as $uid) {
				$rtn[] = self::getEmailByUserId($uid);
			}
		}
		return $rtn;
	}

	public static function dedupToEmailList($to) {
		if (empty($to) || !is_array($to))
			return $to;
		else {
			$rtn = array();
			$email = array_pop($to);
			while ($email !== null) {
				if (!empty($email) && !in_array($email,$to)) $rtn[] = $email;
				$email = array_pop($to);
			} 
			return array_reverse($rtn);
		}
	}
	
	public static function dedupCcEmailList($cc, $to) {
		if (empty($cc) || !is_array($cc))
			return $cc;
		else {
			$rtn = array();
			$email = array_pop($cc);
			while ($email !== null) {
				if (!empty($email) && !in_array($email,$cc)) {
					if (empty($to)) {
						$rtn[] = $email;
					} else {
						if (!is_array($to)) {
							if ($to!=$email) $rtn[] = $email;
						} else {
							if (!in_array($email,$to)) $rtn[]= $email;
						}
					}
				}
				$email = array_pop($cc);
			} 
			return array_reverse($rtn);
		}
	}

	public static function getCityName($code) {
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select name from security$suffix.sec_city where code='$code'";
		return Yii::app()->db->createCommand($sql)->queryScalar();
	}

	public static function getInstalledSystemList() {
		$rtn = array();
		$systems = General::systemMapping();
		foreach ($systems as $key=>$value) {
			$rtn[$key] = Yii::t('app',$value['name']);
		}
		return $rtn;
	}

	public static function getInstalledSystemFunctions() {
		$rtn = array();
		$sysid = Yii::app()->user->system();
		$basePath = Yii::app()->basePath;
		$systems = General::systemMapping();
		$cpathid = end(explode('/',$systems[$sysid]['webroot']));
		foreach ($systems as $key=>$value) {
			$rtn[$key] = array('name'=>$value['name'], 'item'=>array());
			$pathid = end(explode('/',$systems[$key]['webroot']));
			$confFile = ((strpos($basePath, '/'.$pathid.'/')===false) ? str_replace('/'.$cpathid.'/','/'.$pathid.'/',$basePath) : $basePath).'/config/menu.php';
			$menuitems = require($confFile);
			foreach ($menuitems as $group=>$items) {
				foreach ($items['items'] as $k=>$v){
					$aid = $v['access'];
					$rtn[$key]['item'][$group][$aid]['name'] = $k;
					$rtn[$key]['item'][$group][$aid]['tag'] = isset($v['tag']) ? $v['tag'] : '';
				}
			}
			
			$confFile = ((strpos($basePath, '/'.$pathid.'/')===false) ? str_replace('/'.$cpathid.'/','/'.$pathid.'/',$basePath) : $basePath).'/config/control.php';
			if (file_exists($confFile)) {
				$cntitems = require($confFile);
				foreach ($cntitems as $name=>$items) {
					$aid = $items['access'];
					$rtn[$key]['item']['zzcontrol'][$aid]['name'] = $name;
					$rtn[$key]['item']['zzcontrol'][$aid]['tag'] = '';
				}
			}
		}
		return $rtn;
	}

	public function systemMapping() {
		$rtn = require(Yii::app()->basePath.'/config/system.php');
		return $rtn;
	}

	public static function getLocaleAppLabels() {
		$rtn = array();
		$sysid = Yii::app()->user->system();
		$basePath = Yii::app()->basePath;
		$lang = Yii::app()->language;
		if (Yii::app()->sourceLanguage!=$lang) {
			$systems = General::systemMapping();
			$cpathid = end(explode('/',$systems[$sysid]['webroot']));
			foreach ($systems as $key=>$value) {
				$pathid = end(explode('/',$systems[$key]['webroot']));
				$msgFile = ((strpos($basePath, '/'.$pathid.'/')===false) ? str_replace('/'.$cpathid.'/','/'.$pathid.'/',$basePath) : $basePath)
					.'/messages/'.$lang.'/app.php';
				$tmp = require($msgFile);
				$rtn = array_merge($rtn, $tmp);
			}
		}
		return $rtn;
	}
	
	public static function getJobStatusDesc($invalue) {
		switch ($invalue) {
			case 'P':
				return Yii::t('app','Pending');
				break;
			case 'I':
				return Yii::t('app','In Progress');
				break;
			case 'C':
				return Yii::t('app','Complete');
				break;
			case 'F':
				return Yii::t('app','Fail');
				break;
			case 'E':
				return Yii::t('app','Sent');
				break;
			default:
				return '';
		}
	}

}

?>
