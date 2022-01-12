<?php

class AuditCreditController extends Controller
{
	public $function_id='GA01';
	public $type=2;

	public function init(){
	    $this->type = key_exists("type",$_GET)?$_GET["type"]:2;
	    $this->type = in_array($this->type,array(1,2))?$this->type:2;
        $this->function_id = $this->type==2?"GA01":"GA03";
	    parent::init();
    }

    public function filters()
    {
        return array(
            'enforceSessionExpiration',
            'enforceNoConcurrentLogin',
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('edit','reject','audit'),
                'expression'=>array('AuditCreditController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('AuditCreditController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('FileDownload'),
                'expression'=>array('AuditCreditController','allowDownload'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        $type = key_exists("type",$_GET)?$_GET["type"]:2;
        $type = $type==2?"GA01":"GA03";
        return Yii::app()->user->validRWFunction($type);
    }

    public static function allowReadOnly() {
        $type = key_exists("type",$_GET)?$_GET["type"]:2;
        $type = $type==2?"GA01":"GA03";
        return Yii::app()->user->validFunction($type);
    }

    public static function allowDownload() {
        return Yii::app()->user->validFunction("GA01")||Yii::app()->user->validFunction("GA03");
    }

	public function actionIndex($pageNum=0)
	{
		$model = new AuditCreditList;
		$type = $this->type;
		if (isset($_POST['AuditCreditList'])) {
			$model->attributes = $_POST['AuditCreditList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['auditCredit_ya0'.$type]) && !empty($session['auditCredit_ya0'.$type])) {
				$criteria = $session['auditCredit_ya0'.$type];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum,$type);
		$this->render('index',array('model'=>$model));
	}

	public function actionAudit()
	{
		if (isset($_POST['AuditCreditForm'])) {
			$model = new AuditCreditForm("audit");
			$model->attributes = $_POST['AuditCreditForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('auditCredit/index',array('index'=>$model->id,'type'=>$this->type)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditCredit/edit',array('index'=>$model->id,'type'=>$this->type)));
			}
		}
	}

	public function actionReject()
	{
		if (isset($_POST['AuditCreditForm'])) {
			$model = new AuditCreditForm("reject");
			$model->attributes = $_POST['AuditCreditForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('auditCredit/index',array('type'=>$this->type)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditCredit/edit',array('index'=>$model->id,'type'=>$this->type)));
			}
		}
	}

	public function actionView($index)
	{
		$model = new AuditCreditForm('view');
		if (!$model->retrieveData($index,$this->type)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionEdit($index)
	{
		$model = new AuditCreditForm('edit');
		if (!$model->retrieveData($index,$this->type)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}


    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from cy_credit_request where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'RequestCreditForm');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }
}
