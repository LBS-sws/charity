<?php

class PrizeTypeController extends Controller
{
	public $function_id='SS02';
	
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
                'actions'=>array('new','edit','delete','save'),
                'expression'=>array('PrizeTypeController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index'),
                'expression'=>array('PrizeTypeController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('SS02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SS02');
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new PrizeTypeList;
		if (isset($_POST['PrizeTypeList'])) {
			$model->attributes = $_POST['PrizeTypeList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['prizeType_op01']) && !empty($session['prizeType_op01'])) {
				$criteria = $session['prizeType_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['PrizeTypeForm'])) {
			$model = new PrizeTypeForm($_POST['PrizeTypeForm']['scenario']);
			$model->attributes = $_POST['PrizeTypeForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('prizeType/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new PrizeTypeForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionNew()
    {
        $model = new PrizeTypeForm('new');
        $this->render('form',array('model'=>$model,));
    }

	public function actionEdit($index)
	{
		$model = new PrizeTypeForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new PrizeTypeForm('delete');
        if (isset($_POST['PrizeTypeForm'])) {
            $model->attributes = $_POST['PrizeTypeForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('prizeType/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t("dialog","This record is already in use"));
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('prizeType/index'));
        }
    }

    public function actionFileupload($doctype) {
        $model = new PrizeTypeForm();
        if (isset($_POST['PrizeTypeForm'])) {
            $model->attributes = $_POST['PrizeTypeForm'];

            $id = ($_POST['PrizeTypeForm']['scenario']=='new') ? 0 : $model->id;
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
        $model = new PrizeTypeForm();
        if (isset($_POST['PrizeTypeForm'])) {
            $model->attributes = $_POST['PrizeTypeForm'];

            $docman = new DocMan($model->docType,$model->id,'PrizeTypeForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from gr_prize_type where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'PrizeTypeForm');
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
