<?php

class RequestCreditController extends Controller
{
	public $function_id='CY01';
	
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
                'actions'=>array('new','edit','save','delete','audit','fileupload','fileRemove','ajaxCreditType'),
                'expression'=>array('RequestCreditController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload'),
                'expression'=>array('RequestCreditController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('CY01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('CY01');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new RequestCreditList();
		if (isset($_POST['RequestCreditList'])) {
			$model->attributes = $_POST['RequestCreditList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['requestCredit_op01']) && !empty($session['requestCredit_op01'])) {
				$criteria = $session['requestCredit_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionSave()
	{
		if (isset($_POST['RequestCreditForm'])) {
			$model = new RequestCreditForm($_POST['RequestCreditForm']['scenario']);
			$model->attributes = $_POST['RequestCreditForm'];
			if ($model->validate()) {
			    $model->state = 0;
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('requestCredit/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionAudit()
	{
		if (isset($_POST['RequestCreditForm'])) {
			$model = new RequestCreditForm($_POST['RequestCreditForm']['scenario']);
			$model->attributes = $_POST['RequestCreditForm'];
			if ($model->validate()) {
                $model->state = 1;
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('requestCredit/edit',array('index'=>$model->id)));
			} else {
                $model->state = 0;
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

    public function actionEdit($index){
        $model = new RequestCreditForm('edit');
        if($model->validateNowUser(true)){
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }

    public function actionView($index){
        $model = new RequestCreditForm('view');
        if($model->validateNowUser(true)){
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }

    public function actionNew()
    {
        $model = new RequestCreditForm('new');
        $model->apply_date = date("Y-m-d");
        if($model->validateNowUser(true)){
            $this->render('form',array('model'=>$model));
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }
    //刪除
    public function actionDelete(){
        $model = new RequestCreditForm('delete');
        if (isset($_POST['RequestCreditForm'])) {
            $model->attributes = $_POST['RequestCreditForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "刪除失敗");
                $this->redirect(Yii::app()->createUrl('requestCredit/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('requestCredit/index'));
    }

    public function actionFileupload($doctype) {
        $model = new RequestCreditForm();
        if (isset($_POST['RequestCreditForm'])) {
            $model->attributes = $_POST['RequestCreditForm'];

            $id = ($_POST['RequestCreditForm']['scenario']=='new') ? 0 : $model->id;
            $docman = new DocMan($model->docType,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
            $docman->fileUpload();
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileRemove($doctype) {
        $model = new RequestCreditForm();
        if (isset($_POST['RequestCreditForm'])) {
            $model->attributes = $_POST['RequestCreditForm'];

            $docman = new DocMan($model->docType,$model->id,'RequestCreditForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
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

    //ajax
    public function actionAjaxCreditType(){
        if(Yii::app()->request->isAjaxRequest){
            $creditType = $_POST["creditType"];
            $arr = CreditTypeForm::getCreditTypeListToCreditType($creditType);
            if(empty($arr)){
                echo CJSON::encode(array("status"=>0));
            }else{
                echo  CJSON::encode(array("status"=>1,"list"=>$arr));
            }
        }
    }
}
