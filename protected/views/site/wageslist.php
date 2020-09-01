<?php
	$ftrbtn = array();
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>'btnWFClose','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'flowinfodialog',
					'header'=>Yii::t('contract','Wages History'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>
<style>
    #flowinfodialog>div{width: 1000px;}
</style>
<div class="box" id="flow-list" style="max-height: 300px; overflow-y: auto;">
    <?php
    if(!empty($model->historyList)){
        foreach ($model->historyList as $list){
            $tableBodyHtml = "";
            $list["wages_head"] = explode(",",$list["wages_head"]);
            $list["wages_body"] = explode(",",$list["wages_body"]);
            echo "<h4>".Yii::t('contract','Wages Time')."：".date('Y-m', strtotime($list["lcd"]))."</h4>";
            echo "<table class='table table-bordered table-striped table-hover' style='margin-bottom: 5px;'><thead><tr>";
            foreach ($list["wages_head"] as $key => $value){
                echo "<th>$value</th>";
                if(!empty($list["wages_body"][$key])){
                    $tableBodyHtml.="<td>".$list["wages_body"][$key]."</td>";
                }else{
                    $tableBodyHtml.="<td></td>";
                }
            }
            echo "</tr></thead><tbody><tr>".$tableBodyHtml;
            echo "</tr></tbody></table>";
            echo "<div><p class='pull-left'>提交用戶：".$list["lcu"]."</p><p class='pull-right'>审批人：".$list["luu"]."</p></div>";
        }
    }else{
        echo "<h4>".Yii::t("dialog","No File Record")."</h4>";
    }
    ?>
</div>

<?php
	$this->endWidget();
?>
