<?php
class ReportController extends Controller
{
	protected static $actions = array(
						//'salessummary'=>'YB02',
						'creditslist'=>'YB02',
						'prizelist'=>'YB03',
					);
	
	public function filters()
	{
		return array(
			'enforceRegisteredStation',
			'enforceSessionExpiration', 
			'enforceNoConcurrentLogin',
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules() {
		$act = array();
		foreach ($this->action as $key=>$value) { $act[] = $key; }
		return array(
			array('allow', 
				'actions'=>$act,
				'expression'=>array('ReportController','allowExecute'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function actionCreditslist() {
		$this->function_id = self::$actions['creditslist'];
		Yii::app()->session['active_func'] = $this->function_id;

        $model = new ReportY02Form;
        $model->name=Yii::t("app","Charity Credit Report");
        if (isset($_POST['ReportY02Form'])) {
            $model->attributes = $_POST['ReportY02Form'];
            if ($model->validate()) {
                $model->city_allow = Yii::app()->user->city_allow();
                $model->auth_bool = Yii::app()->user->validFunction('ZR03')?1:0;
                $model->addQueueItem();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
            }
        }
        $this->render('form_y02',array('model'=>$model));
    }

    public function actionPrizelist() {
		$this->function_id = self::$actions['prizelist'];
		Yii::app()->session['active_func'] = $this->function_id;
        $model = new ReportY02Form;
        $model->id="RptPrizeList";
        $model->name=Yii::t("app","Charity Prize Report");
        if (isset($_POST['ReportY02Form'])) {
            $model->attributes = $_POST['ReportY02Form'];
            if ($model->validate()) {
                $model->city_allow = Yii::app()->user->city_allow();
                $model->auth_bool = Yii::app()->user->validFunction('ZR03')?1:0;
                $model->addQueueItem();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
            }
        }
        $this->render('form_y02',array('model'=>$model,'submit'=>Yii::app()->createUrl('report/prizelist')));
    }

	public static function allowExecute() {
		return Yii::app()->user->validFunction(self::$actions[Yii::app()->controller->action->id]);
	}
}
?>
