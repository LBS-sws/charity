<?php

class SearchCreditController extends Controller
{
	public $function_id='SR01';
	
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
                'actions'=>array('index','view','FileDownload'),
                'expression'=>array('SearchCreditController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('cancel'),
                'expression'=>array('SearchCreditController','allowReadCancel'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SR01');
    }
    public static function allowReadCancel() {
        return Yii::app()->user->validFunction('ZR01');
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new SearchCreditList;
		if (isset($_POST['SearchCreditList'])) {
			$model->attributes = $_POST['SearchCreditList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['searchCredit_op01']) && !empty($session['searchCredit_op01'])) {
				$criteria = $session['searchCredit_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionView($index)
	{
		$model = new SearchCreditForm('view');
		if (!$model->retrieveData($index)) {
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

    public function actionCancel()
    {
        if (isset($_POST['SearchCreditForm'])) {
            $model = new SearchCreditForm("cancel");
            $model->attributes = $_POST['SearchCreditForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Void Done'));
                $this->redirect(Yii::app()->createUrl('searchCredit/index'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
}
