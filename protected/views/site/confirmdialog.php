<?php
if(empty($content)){
    $content = "<p>".Yii::t('dialog','Are you sure to back?')."</p>";
}
$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'backdialog',
    'header'=>Yii::t('dialog','Back Record'),
    'content'=>$content,
    'footer'=>array(
        TbHtml::button(Yii::t('dialog','OK'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY,'submit'=>$submit)),
        TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
    ),
    'show'=>false,
));
?>