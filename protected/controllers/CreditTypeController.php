<?php

class CreditTypeController extends Controller
{
 	public $function_id='SS01';
	
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
                'actions'=>array('new','edit','delete','save','ajaxDepartment'),
                'expression'=>array('CreditTypeController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('CreditTypeController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('SS01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SS01');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new CreditTypeList;
		if (isset($_POST['CreditTypeList'])) {
			$model->attributes = $_POST['CreditTypeList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['CreditType_op01']) && !empty($session['CreditType_op01'])) {
				$criteria = $session['CreditType_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['CreditTypeForm'])) {
			$model = new CreditTypeForm($_POST['CreditTypeForm']['scenario']);
			$model->attributes = $_POST['CreditTypeForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('creditType/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new CreditTypeForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionNew()
    {
        $model = new CreditTypeForm('new');
        $model->bumen_ex = Yii::t("misc","All");
        $this->render('form',array('model'=>$model,));
    }

	public function actionEdit($index)
	{
		$model = new CreditTypeForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new CreditTypeForm('delete');
        if (isset($_POST['CreditTypeForm'])) {
            $model->attributes = $_POST['CreditTypeForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('creditType/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t("dialog","This record is already in use"));
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('creditType/index'));
        }
    }

    //部門的異步請求
    public function actionAjaxDepartment(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $department = $_POST['department'];
            $arr = CreditTypeForm::searchDepartment($department);
            echo CJSON::encode($arr);
        }else{
            echo "Error:404";
        }
        Yii::app()->end();
    }


    //導入
    public function actionImportIntegral(){
        $model = new UploadExcelForm();
        $img = CUploadedFile::getInstance($model,'file');
        $city = Yii::app()->user->city();
        $path =Yii::app()->basePath."/../upload/";
        if (!file_exists($path)){
            mkdir($path);
        }
        $path =Yii::app()->basePath."/../upload/excel/";
        if (!file_exists($path)){
            mkdir($path);
        }
        $path.=$city."/";
        if (!file_exists($path)){
            mkdir($path);
        }
        if(empty($img)){
            Dialog::message(Yii::t('dialog','Validation Message'), "文件不能为空");
            $this->redirect(Yii::app()->createUrl('question/index',array('index'=>$model->quiz_id)));
        }
        $url = "upload/excel/".$city."/".date("YmdHis").".".$img->getExtensionName();
        $model->file = $img->getName();
        if ($model->file) {
            $img->saveAs($url);
            $loadExcel = new LoadExcel($url);
            $list = $loadExcel->getExcelList();
            $model->loadCreditType($list);
            $this->redirect(Yii::app()->createUrl('creditType/index'));
        }else{
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->redirect(Yii::app()->createUrl('creditType/index'));
        }
    }
}
