<?php
$this->pageTitle=Yii::app()->name . ' - Credit type allocation';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'auditPrize-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Audit Charity Prize'); ?></strong>
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
    <?php
    $search = array(
        'prize_name',
        'prize_point',
        'employee_name',
        'city_name',
    );
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('charity','Audit Prize List'),
        'model'=>$model,
        'viewhdr'=>'//auditPrize/_listhdr',
        'viewdtl'=>'//auditPrize/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>$search,
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
$js = "
$('#start_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('#end_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

