<?php
if (empty($model->id)){
    $this->redirect(Yii::app()->createUrl('searchPrize/index'));
}
$this->pageTitle=Yii::app()->name . ' - searchPrize Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'searchPrize-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('charity','Search Prize Form'); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('searchPrize/index')));
                ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php if (Yii::app()->user->validFunction('ZR02')): ?>
                    <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('dialog','Cancel'), array(
                            'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#canceldialog',)
                    );
                    ?>
                <?php endif; ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'state'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php if ($model->state == 2): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'reject_note',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-5">
                        <?php echo $form->textArea($model, 'reject_note',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
                <legend></legend>
            <?php endif; ?>

            <?php
            $this->renderPartial('//site/prizeAddForm',array(
                'form'=>$form,
                'model'=>$model,
                'readonly'=>(true),
            ));
            ?>
        </div>
    </div>
</section>

<?php
$this->renderPartial('//site/canceldialog');
?>

<?php

$js = "
//取消事件
$('#btnCancelData').on('click',function() {
	$('#canceldialog').modal('hide');
	var elm=$('#btnCancelData');
	jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('searchPrize/cancel')."',{});
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

