<?php

class RequestPrizeController extends Controller
{
	public $function_id='CY02';
	
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
                'actions'=>array('new','save','delete','audit','edit','ajaxPrizeType'),
                'expression'=>array('RequestPrizeController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('RequestPrizeController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('CY02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('CY02');
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new RequestPrizeList();
		if (isset($_POST['RequestPrizeList'])) {
			$model->attributes = $_POST['RequestPrizeList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['requestPrize_op01']) && !empty($session['requestPrize_op01'])) {
				$criteria = $session['requestPrize_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionSave()
	{
		if (isset($_POST['RequestPrizeForm'])) {
			$model = new RequestPrizeForm($_POST['RequestPrizeForm']['scenario']);
			$model->attributes = $_POST['RequestPrizeForm'];
            $model->state = 0;
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('requestPrize/edit',array('index'=>$model->id)));
			} else {
			    $model->prize_point*=$model->apply_num;
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionAudit()
	{
		if (isset($_POST['RequestPrizeForm'])) {
			$model = new RequestPrizeForm($_POST['RequestPrizeForm']['scenario']);
			$model->attributes = $_POST['RequestPrizeForm'];
            $model->state = 1;
			if ($model->validate()) {
				$model->saveData();
                $model->prize_point*=$model->apply_num;
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('requestPrize/edit',array('index'=>$model->id)));
			} else {
                $model->state = 0;
                $model->prize_point*=$model->apply_num;
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

    public function actionEdit($index){
        $model = new RequestPrizeForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index){
        $model = new RequestPrizeForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionNew()
    {
        $model = new RequestPrizeForm('new');
        if($model->validateNowUser(true)){
            $this->render('form',array('model'=>$model));
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }
    //刪除
    public function actionDelete(){
        $model = new RequestPrizeForm('delete');
        if (isset($_POST['RequestPrizeForm'])) {
            $model->attributes = $_POST['RequestPrizeForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "刪除失敗");
                $this->redirect(Yii::app()->createUrl('requestPrize/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('requestPrize/index'));
    }

    //員工可用學分的異步獲取
    public function actionAjaxPrizeType(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $prizeType = key_exists("prizeType",$_POST)?$_POST["prizeType"]:0;
            $apply_num = key_exists("apply_num",$_POST)?$_POST["apply_num"]:0;
            $list = PrizeTypeForm::getPrizeTypeListToId($prizeType);
            if(empty($list)){
                echo CJSON::encode(array("status"=>0,"list"=>$list));
            }else{
                $list["prize_point"] *=$apply_num;
                echo CJSON::encode(array("status"=>1,"list"=>$list));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('requestPrize/index'));
        }
    }

//退回
    public function actionBackPrize()
    {
        if (isset($_POST['RequestPrizeForm'])) {
            $model = new RequestPrizeForm($_POST['RequestPrizeForm']['scenario']);
            $model->attributes = $_POST['RequestPrizeForm'];
            if($model->backPrize()){
                Dialog::message(Yii::t('dialog','Information'), Yii::t('integral','Return to success.'));
                $this->redirect(Yii::app()->createUrl('requestPrize/edit',array('index'=>$model->id)));
            }else{
                $message="退回失敗，請與管理員聯繫";
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
}
