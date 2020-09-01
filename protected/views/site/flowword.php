<?php
	$ftrbtn = array();
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>'btnWFClose','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'flowinfodialog',
					'header'=>$model->name,
					'footer'=>$ftrbtn,
					'show'=>false,
                    'htmlOptions'=>array('class' => 'testBox')
				));
?>

<div class="box" id="flow-list" style="max-height: 650px; overflow-y: auto;">
    <?php echo $model->word_html; ?>
</div>

<?php
	$this->endWidget(); 
?>
<style>
    .testBox>.modal-dialog{width: 700px;}
</style>
