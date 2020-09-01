<?php
echo '<form action="'.Yii::app()->createUrl('employ/attachmentUp').'" method="post" enctype="multipart/form-data" class="form-horizontal" id="UploadFileForm" name="UploadFileForm" onsubmit="return false;">';
$ftrbtn = array();
if(!$ronly){
    $ftrbtn[] = TbHtml::button(Yii::t('dialog','Upload'), array('id'=>"importUp" ,'data-url'=>Yii::app()->createUrl('employ/attachmentUp')));
}
$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>"btnWFClose",'data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
$this->beginWidget('bootstrap.widgets.TbModal', array(
    'id'=>'fileuploadpayreq',
    'header'=>Yii::t('contract','Attachment'),
    'footer'=>$ftrbtn,
    'show'=>false,
));
?>

<div class="box" id="flow-list" style="max-height: 300px; overflow-y: auto;">
    <table id="attachmentList" class="table table-hover">
        <thead>
        <tr>
            <th></th>
            <th><?php echo Yii::t("contract","Attachment Name"); ?></th>
            <th><?php echo Yii::t("dialog","Date"); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        if(!empty($model->attachment)){
            foreach ($model->attachment as $attachment){
                echo "<tr>";
                echo "<td>";
                echo TbHtml::link("<span class='fa fa-download'></span>",Yii::app()->createUrl('employ/attachmentDown',array("index"=>$attachment["id"])));
                if(!$ronly){
                    echo "&nbsp;&nbsp;";
                    echo TbHtml::link("<span class='fa fa-close'></span>","javascript:void(0);",
                        array("class"=>"attachmentDelete","data-url"=>Yii::app()->createUrl('employ/attachmentDelete'),"data-id"=>$attachment["id"],"data-type"=>$type)
                    );
                }
                echo "</td>";
                echo "<td>".$attachment["file_name"]."</td>";
                echo "<td>".$attachment["lcd"]."</td>";
                echo "</tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>
<?php if (!$ronly): ?>
<div class="form-group">
    <input type="hidden" name="type" value="<?php echo $type;?>">
    <label class="pull-left control-label" style="padding-left: 15px;"><?php echo Yii::t("contract","Attachment"); ?></label>
    <div class="col-sm-7">
        <input id="file_fasd" class=" form-control" type="file" name="UploadFileForm[file]">
    </div>
</div>
<?php endif; ?>

<?php
$this->endWidget();
echo "</form>";
?>
