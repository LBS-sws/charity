<?php
$this->pageTitle=Yii::app()->name . ' - Credit type allocation';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'creditType-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Charity Credit type'); ?></strong>
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
    <div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction('SS01'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('creditType/new'),
                    ));
                ?>
            </div>
        </div>
    </div>
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('charity','Credit Type List'),
			'model'=>$model,
				'viewhdr'=>'//creditType/_listhdr',
				'viewdtl'=>'//creditType/_listdtl',
				'search'=>array(
							'charity_code',
							'charity_name',
							'charity_point',
							'rule',
						),
		));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

<?php
if (Yii::app()->user->validRWFunction('SS01'))
    //$this->renderPartial('//site/importIntegral',array('name'=>"UploadExcelForm","model"=>$model,"submit"=>Yii::app()->createUrl('creditType/importIntegral')));
?>
<?php
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

