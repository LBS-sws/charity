<?php

class SearchSumController extends Controller
{
	public $function_id='SR02';

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
                'actions'=>array('index'),
                'expression'=>array('SearchSumController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SR02');
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new SearchSumList;
        $model->year = date("Y");
		if (isset($_POST['SearchSumList'])) {
			$model->attributes = $_POST['SearchSumList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['searchSum_op01']) && !empty($session['searchSum_op01'])) {
				$criteria = $session['searchSum_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}
}
