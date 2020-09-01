<?php
$this->pageTitle=Yii::app()->name . ' - auditPrize Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'auditPrize-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('charity','Audit Prize Form'); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('auditPrize/index')));
                ?>
                <?php if ($model->scenario!='view' && $model->state == 1): ?>
                    <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('charity','Audit'), array(
                        'submit'=>Yii::app()->createUrl('auditPrize/audit')));
                    ?>
                <?php endif ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='view' && $model->state == 1): ?>
                    <?php
                    echo TbHtml::button('<span class="fa fa-mail-reply-all"></span> '.Yii::t('charity','Rejected'), array(
                        'name'=>'btnJect','id'=>'btnJect','data-toggle'=>'modal','data-target'=>'#jectdialog'));
                    ?>
                <?php endif ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'scenario'); ?>
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
                'readonly'=>($model->scenario=='view'||$model->state == 1||$model->state == 3),
            ));
            ?>


        </div>
    </div>
</section>

<?php
$this->renderPartial('//site/ject',array('model'=>$model,'form'=>$form,'rejectName'=>"reject_note",'submit'=>Yii::app()->createUrl('auditPrize/reject')));
?>
<?php

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

