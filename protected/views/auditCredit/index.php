<?php
$this->pageTitle=Yii::app()->name . ' - auditCredit Info';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'auditCredit-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Audit Charity Credit'); ?></strong>
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
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('charity','Charity Credit List'),
			'model'=>$model,
				'viewhdr'=>'//auditCredit/_listhdr',
				'viewdtl'=>'//auditCredit/_listdtl',
				'search'=>array(
                    'charity_name',
                    'credit_point',
                    'employee_name',
                    'city_name',
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
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

