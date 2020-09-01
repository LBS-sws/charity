<?php

class SearchPrizeController extends Controller
{
	public $function_id='SR03';

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
                'actions'=>array('index','view'),
                'expression'=>array('SearchPrizeController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('cancel'),
                'expression'=>array('SearchPrizeController','allowReadCancel'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SR03');
    }

    public static function allowReadCancel() {
        return Yii::app()->user->validFunction('ZR02');
    }

    public function actionIndex($pageNum=0)
    {
        $model = new SearchPrizeList;
        if (isset($_POST['SearchPrizeList'])) {
            $model->attributes = $_POST['SearchPrizeList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['searchPrize_ya01']) && !empty($session['searchPrize_ya01'])) {
                $criteria = $session['searchPrize_ya01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionView($index)
    {
        $model = new SearchPrizeForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionCancel()
    {
        if (isset($_POST['SearchPrizeForm'])) {
            $model = new SearchPrizeForm("cancel");
            $model->attributes = $_POST['SearchPrizeForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Void Done'));
                $this->redirect(Yii::app()->createUrl('searchPrize/index'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

}
