<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('prizeType/index'));
}
$this->pageTitle=Yii::app()->name . ' - Credit type allocation';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'prizeType-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('charity','Prize Type Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('prizeType/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php
            if($model->scenario!='new'){
                echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                    'submit'=>Yii::app()->createUrl('prizeType/new'),
                ));
            }
            echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('prizeType/save')));
			?>
<?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
            );
            ?>
        <?php endif ?>
	</div>
            <!--
            <div class="btn-group pull-right" role="group">
                <?php
                $counter = ($model->no_of_attm['cprize'] > 0) ? ' <span id="doccprize" class="label label-info">'.$model->no_of_attm['cprize'].'</span>' : ' <span id="doccprize"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadcprize',)
                );
                ?>
            </div>
            -->
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>


            <div class="form-group">
                <?php echo $form->labelEx($model,'prize_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'prize_name',
                        array('size'=>40,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'prize_point',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'prize_point',
                        array('min'=>1,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'z_index',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'z_index',
                        array('min'=>0,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <div class="col-sm-10 col-sm-offset-2 text-warning">
                    <p class="form-control-static"><?php echo Yii::t('charity','z_index hint'); ?></p>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'prize_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-6">
                    <?php echo $form->textArea($model, 'prize_remark',
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
?>

<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'CPRIZE',
    'header'=>Yii::t('dialog','File Attachment'),
    'ronly'=>($model->scenario=='view'),
));
?>

<?php
$js = "
    $('.tries_change').on('change',function(){
        if($(this).val()==0){
            $(this).parent('.input-group-btn').next('.tries_change_info').hide();
        }else{
            $(this).parent('.input-group-btn').next('.tries_change_info').show();
        }
    }).trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('prizeType/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);
Script::genFileUpload($model,$form->id,'CPRIZE');
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


