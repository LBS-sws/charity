<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('auditCredit/index'));
}
$this->pageTitle=Yii::app()->name . ' - auditCredit Info';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'auditCredit-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .input-group .input-group-addon{background: #eee;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('charity','Audit Credit Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('auditCredit/index')));
		?>
        <?php if ($model->scenario!='view' && $model->state == 1): ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('charity','Audit'), array(
                'submit'=>Yii::app()->createUrl('auditCredit/audit')));
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

                <?php
                $counter = ($model->no_of_attm['cyral'] > 0) ? ' <span id="doccyral" class="label label-info">'.$model->no_of_attm['cyral'].'</span>' : ' <span id="doccyral"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadcyral',)
                );
                ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'state'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'employee_name',
                        array('readonly'=>(true))
                    ); ?>
                    <?php echo $form->hiddenField($model, 'employee_id'); ?>
                </div>
            </div>

            <?php
            $this->renderPartial('//site/charityAddForm',array(
                'form'=>$form,
                'model'=>$model,
                'readonly'=>(true),
            ));
            ?>
            <legend></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'s_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-6">
                    <?php echo $form->textArea($model, 's_remark',
                        array('readonly'=>(true),'rows'=>4)
                    );
                    ?>
                </div>
            </div>
		</div>
	</div>
</section>


<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'CYRAL',
    'header'=>Yii::t('dialog','File Attachment'),
    'ronly'=>(true),
));
?>
<?php
$this->renderPartial('//site/ject',array('model'=>$model,'form'=>$form,'rejectName'=>"reject_note",'submit'=>Yii::app()->createUrl('auditCredit/reject')));
?>
<?php
Script::genFileUpload($model,$form->id,'CYRAL');

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


