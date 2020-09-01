<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('creditType/index'));
}
$this->pageTitle=Yii::app()->name . ' - Credit type allocation';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'creditType-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('charity','Credit Type Form'); ?></strong>
	</h1>
<!--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Layout</a></li>
		<li class="active">Top Navigation</li>
	</ol>
-->
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('creditType/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php
        if($model->scenario!='new') {
            echo TbHtml::button('<span class="fa fa-file-o"></span> ' . Yii::t('misc', 'Add'), array(
                'submit' => Yii::app()->createUrl('creditType/new'),
            ));
        }
            echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('creditType/save')));
			?>
<?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
            );
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'bumen',array("id"=>"bumen")); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'charity_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'charity_code',
                        array('readonly'=>$model->scenario=='view')
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'charity_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'charity_name',
                        array('size'=>40,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'charity_point',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'charity_point',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'year_sw',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'year_sw',array(Yii::t("charity","off"),Yii::t("charity","on")),
                        array('readonly'=>($model->scenario=='view'),'id'=>'yearSw')
                    ); ?>
                </div>
            </div>
            <div class="form-group" style="display: none;">
                <?php echo $form->labelEx($model,'year_max',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'year_max',
                        array('readonly'=>($model->scenario=='view'),'id'=>'yearNum')
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'bumen_ex',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'bumen_ex',
                        array('readonly'=>(true),'rows'=>4,"id"=>"bumen_ex")
                    ); ?>
                </div>
                <div class="col-sm-2">
                    <?php
                    echo TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('dialog','Select'),
                        array('data-toggle'=>'modal','data-target'=>'#bumendialog',)
                    );
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'z_index',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'z_index',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <div class="col-sm-10 col-sm-offset-2 text-warning">
                    <p class="form-control-static"><?php echo Yii::t('charity','z_index hint'); ?></p>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'rule',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-6">
                    <?php echo $form->textArea($model, 'rule',
                        array('readonly'=>($model->scenario=='view'),'rows'=>4)
                    );
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-6">
                    <?php echo $form->textArea($model, 'remark',
                        array('readonly'=>($model->scenario=='view'),'rows'=>4)
                    );
                    ?>
                </div>
            </div>
		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/removedialog');
$this->renderPartial('//site/bumendialog');
?>

<?php
$js = "
        $('#yearSw').on('change',function () {
            if($(this).val()==1){
                $('#yearNum').parents('.form-group:first').slideDown(100);
            }else{
                $('#yearNum').parents('.form-group:first').slideUp(100);
            }
        }).trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('creditType/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


