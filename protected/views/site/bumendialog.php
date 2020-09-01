<style>
    .checkbox{padding-left: 10px;}
</style>
<?php
$ftrbtn = array();
//$ftrbtn[] = TbHtml::button(Yii::t('dialog','OK'), array('id'=>"btnSelect",'data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
$this->beginWidget('bootstrap.widgets.TbModal', array(
    'id'=>'bumendialog',
    'header'=>Yii::t('misc','Select').Yii::t('charity','department'),
    'footer'=>$ftrbtn,
    'show'=>false,
));
?>

<div class="form-group">
    <div class="col-lg-12">
        <div class="input-group">
            <?php echo TbHtml::textField('department',"",array("class"=>"form-control","id"=>"department")); ?>
            <span class="input-group-btn">
                <?php echo TbHtml::button(Yii::t('misc','Search'), array('id'=>'search','color'=>TbHtml::BUTTON_COLOR_DEFAULT)); ?>
            </span>
        </div>
        <div id="departmentDiv" style="border: 1px solid #d2d6de;height: 200px;overflow-x: hidden;overflow-y: scroll">

        </div>
    </div>
</div>

<div class="form-group" style="margin-bottom: 0px;border-top:1px solid #e5e5e5">
    <div class="col-sm-12">
        <span class="control-label"><?php echo Yii::t("charity","shortcuts");?>：</span>
        <div class="checkbox-inline" style=""><label><?php echo TbHtml::checkBox("short_all","",array("id","short_all")).Yii::t("charity","all select");?></label></div>
    </div>
</div>

<?php
$this->endWidget();
?>
<script>
    $(function () {
        $("#short_all").on("click",function () {
            if($(this).is(":checked")){
                $("#departmentDiv .check_dev:not(:checked)").trigger("click");
            }else{
                $("#departmentDiv .check_dev:checked").trigger("click");
            }
        });
        $("#short_aga").on("click",function () {
            $("#departmentDiv .check_dev").trigger("click");
        });
        var ajaxBool = true;
        $("#search").on("click",function () {
            if(ajaxBool){
                ajaxBool = false;
                $.ajax({
                    type: "POST",
                    url: "<?php echo Yii::app()->createUrl('creditType/ajaxDepartment');?>",
                    dataType: "json",
                    data: {
                        "department":$("#department").val()
                    },
                    success: function(msg){
                        var bumen = $("#bumen").val();
                        ajaxBool = true;
                        var html = "";
                        $.each(msg,function (item, value) {
                            if(bumen.indexOf(","+item+",")>-1){
                                html+='<div class="checkbox"><label><input type="checkbox" checked class="check_dev" value="'+item+'">'+value+'</label></div>';
                            }else {
                                html+='<div class="checkbox"><label><input type="checkbox" class="check_dev" value="'+item+'">'+value+'</label></div>'
                            }
                        });
                        $("#departmentDiv").html(html);
                    },
                    error:function () {
                        ajaxBool = true;
                    }
                });
            }
        });
        $("#departmentDiv").delegate(".check_dev","change",function () {
            var key = $(this).val()+",";
            var value = $(this).parent("label").text()+",";
            var bumen = $("#bumen").val();
            var bumen_ex = $("#bumen_ex").val();
            if(bumen_ex == "全部"){
                bumen_ex = "";
            }
            if(bumen.indexOf(",") !== 0){
                bumen= ","+bumen;
            }
            if(bumen_ex.indexOf(",") !== 0){
                bumen_ex= ","+bumen_ex;
            }
            bumen = bumen.split(","+key).join(",");
            bumen_ex=bumen_ex.split(","+value).join(",");
            if($(this).is(":checked")){
                bumen+=key;
                bumen_ex+=value;
            }
            if(bumen_ex == ""||bumen_ex == ","){
                bumen_ex = "全部";
            }
            if(bumen == ","){
                bumen = "";
            }
            $("#bumen").val(bumen);
            $("#bumen_ex").val(bumen_ex);
        })
    });
</script>