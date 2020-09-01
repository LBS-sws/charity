
<div class="form-group">
    <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'employee_name',
            array('readonly'=>(true))
        ); ?>
        <?php echo $form->hiddenField($model, 'employee_id',array("id"=>"employee_id")); ?>
    </div>
    <?php if (get_class($model) == "RequestPrizeForm"&&!$readonly): ?>

        <div class="col-sm-5">
            <p class="form-control-static">
                <?php echo Yii::t("charity","Available Gift")."：".$model->getCreditSumToYear()["end_num"]; ?>
            </p>
        </div>
    <?php endif; ?>
</div>

<div class="form-group">
    <?php echo $form->labelEx($model,'prize_type',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListTwo($model, 'prize_type',PrizeTypeForm::getPrizeTypeList(),
            array('readonly'=>($readonly),'id'=>'prize_type','data-num'=>$model->prize_point)
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'prize_point',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'prize_point',
            array('readonly'=>(true),'id'=>'prize_point')
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'prize_remark',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textArea($model, 'prize_remark',
            array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>(true),'id'=>'prize_remark')
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'apply_num',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->numberField($model, 'apply_num',
            array('readonly'=>($readonly),'id'=>'apply_num','min'=>0)
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textArea($model, 'remark',
            array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($readonly))
        ); ?>
    </div>
</div>
<?php if ($model->scenario!='new'): ?>
    <div class="form-group">
        <?php echo $form->labelEx($model,'apply_date',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->textField($model, 'apply_date',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif ?>

<script>
    $(function () {

        $("#prize_type,#apply_num").on("change",function () {
            var id = $("#prize_type").val();
            var apply_num = $("#apply_num").val();
            $.ajax({
                type: "post",
                url: "<?php echo Yii::app()->createUrl('requestPrize/ajaxPrizeType');?>",
                data: {"prizeType":id,"apply_num":apply_num},
                dataType: "json",
                success: function(data){
                    if(data.status == 1){
                        var list = data.list;
                        $("#prize_point").val(list["prize_point"]);
                        $("#prize_remark").val(list["prize_remark"]);
                    }
                }
            });
        });
    })
</script>