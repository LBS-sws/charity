<?php
$this->pageTitle=Yii::app()->name . ' - requestPrize Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'requestPrize-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('charity','Request Prize Form'); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('requestPrize/index')));
                ?>

                <?php if ($model->scenario!='view' && $model->state != 1 && $model->state != 3): ?>
                    <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                        'submit'=>Yii::app()->createUrl('requestPrize/save')));
                    ?>
                    <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('charity','For Audit'), array(
                        'submit'=>Yii::app()->createUrl('requestPrize/audit')));
                    ?>
                <?php endif ?>
                <?php if ($model->scenario=='edit'&& ($model->state == 0||$model->state == 2)): ?>
                    <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                            'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                    );
                    ?>
                <?php endif; ?>
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
$this->renderPartial('//site/removedialog');
?>
<?php

$js = Script::genDeleteData(Yii::app()->createUrl('requestPrize/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

