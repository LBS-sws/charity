
<div class="form-group">
    <?php echo $form->labelEx($model,'apply_date',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textField($model, 'apply_date',
                array('class'=>'form-control pull-right','readonly'=>($readonly),'id'=>"apply_date"));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo TbHtml::label(Yii::t('charity','expiration date'),"",array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo TbHtml::textField('exp_date','',
                array('class'=>'form-control pull-right','readonly'=>(true),'id'=>"exp_date"));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'credit_type',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-6">
        <?php echo $form->dropDownListTwo($model, 'credit_type',CreditTypeForm::getCreditTypeList($model->position),
            array('readonly'=>($readonly),'id'=>'set_id')
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'credit_point',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'credit_point',
            array('readonly'=>(true),'id'=>'integral')
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'rule',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textArea($model, 'rule',
            array('readonly'=>(true),'rows'=>4,'cols'=>50,'id'=>'rule')
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

<script>
    $(function () {
        $("#set_id").on("change",function () {
            var id = $(this).val();
            $.ajax({
                type: "post",
                url: "<?php echo Yii::app()->createUrl('requestCredit/ajaxCreditType');?>",
                data: {"creditType":id},
                dataType: "json",
                success: function(data){
                    if(data.status == 1){
                        var list = data.list;
                        $("#integral").val(list["charity_point"]);
                        $("#rule").val(list["rule"]);
                    }
                }
            });
        });
        $("#apply_date").on("change",function () {
            var value = $(this).val();
            if(value != ""){
                value = value.split("-")[0];
                value = value.split("\/")[0];
                value = parseInt(value,10)+4;
                $("#exp_date").val(value+"-12-31");
            }
        }).trigger("change");
    })
</script>