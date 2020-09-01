<?php
$this->pageTitle=Yii::app()->name . ' - Credit type allocation';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'searchSum-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Charity Credit Search Sum'); ?></strong>
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
            <p class="pull-left"><?php echo Yii::t("charity","Total credits = all credits for the year");?></p>
            <p class="pull-right"><?php echo Yii::t("charity","Available credits = total credits - credits deducted from the award application");?></p>
        </div>
    </div>
    <?php
    $search = array(
        'city_name',
        'employee_name',
    );
    $search_add_html="";
    $modelName = get_class($model);
    $search_add_html .= '<select class="form-control" id="selectYearChange" name="'.$modelName.'[year]">';
    foreach ($model->getYearList() as $row) {
        $search_add_html .= '<option value="'.$row["value"].'"';
        if($row["value"] == $model->year){
            $search_add_html.="selected ";
        }
        $search_add_html .='style="color:'.$row["color"].'">'.$row["name"].'</option>';
    }
    $search_add_html .='</select>';
    //$search_add_html .= TbHtml::dropDownList($modelName.'[year]',$model->year,$model->getYearList(),array("class"=>"form-control"));

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('charity','Search Total List'),
        'model'=>$model,
        'viewhdr'=>'//searchSum/_listhdr',
        'viewdtl'=>'//searchSum/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search_add_html'=>$search_add_html,
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
$('#selectYearChange').on('change',function(){
    $('form:first').submit();
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

