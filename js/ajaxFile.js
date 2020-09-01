
(function ($) {
    $.extend($.fn, {
        upload: function (settings) {

            var options = $.extend({
                fileType: "gif|jpg|jpeg|png|bmp",  //允许的文件格式
                uploadUrl: "", //上传URL地址
                width: "",   //图片显示的宽度
                height: 100,   //图片显示的高度
                imgSelector: ".imgdiv",   //图片选择器
                uploadData: {},   //上传时需要附加的参数

                beforeSubmitFn: "beforeUpload",  //上传前执行的方法 原型 beforeSubmit(arr, $form, options);
                successFn: "uploadSuccess",  //上传成功后执行的方法 uploadSuccess(response, statusText, xhr, $this)
                errorFn: "uploadError"   //上传失败后执行的方法
            }, settings);

            //上传准备函数
            var methods = {
                //验证文件格式
                checkFile: function (filename) {
                    var pos = filename.lastIndexOf(".");
                    var str = filename.substring(pos, filename.length);
                    var str1 = str.toLowerCase();
                    if (typeof options.fileType !== 'string') { options.fileType = "gif|jpg|jpeg|png|bmp"; }
                    var re = new RegExp("\.(" + options.fileType + ")$");
                    return re.test(str1);
                },
                //创建表单
                createForm: function () {
                    var $form = document.createElement("form");
                    $form.action = options.uploadUrl;
                    $form.method = "post";
                    $form.enctype = "multipart/form-data";
                    $form.style.display = "none";
                    //将表单加当document上，
                    document.body.appendChild($form); //创建表单后一定要加上这句否则得到的form不能上传。document后要加上body,否则火狐下不行。
                    return $($form);
                },
                //创建图片
                createImage: function () {
                    //不能用 new Image() 来创建图片，否则ie下不能改变img 的宽高
                    var img = $(document.createElement("img"));
                    img.attr({ "title": "双击图片可删除图片！" });
                    if (options.width !== "") {
                        img.attr({ "width": options.width });
                    }
                    if (options.height !== "") {
                        img.attr({ "height": options.height });
                    }
                    return img;
                },
                showImage: function (filePath, $parent) {
                    var $img = methods.createImage();
                    $parent.find(options.imgSelector).find("img").remove();
                    //要先append再给img赋值，否则在ie下不能缩小宽度。
                    $img.appendTo($parent.find(options.imgSelector));
                    $img.attr("src", filePath);
                    this.bindDelete($parent);
                },
                onload: function ($parent) {
                }
            };
            //上传主函数
            this.each(function () {
                var $this = $(this);
                //methods.onload($this.parent());
                $this.bind("change", function () {
                    var $fileInput = $(this);
                    var fileBox = $fileInput.parent();
                    if($fileInput.hasClass("readonly")){
                        return false;
                    }

                    $fileInput.attr("name","UploadImgForm[file]");
                    if ($fileInput.val() === "") {
                        alert("请选择要上传的图片！");
                        return false;
                    }
                    //验证图片
                    if (!methods.checkFile($fileInput.val())) {
                        alert("文件格式不正确，只能上传格式为：" + options.fileType + "的文件。");
                        return false;
                    }

                    //创建表单
                    var $form = methods.createForm();

                    //把上传控件附加到表单
                    $fileInput.appendTo($form);
                    fileBox.append("<label class='uploading-label'>正在上传...</label>");
                    //$this.prop("disabled", true);

                    //构建ajaxSubmit参数
                    var data = {};
                    data.data = options.uploadData;
                    data.type = "POST";
                    data.dataType = "JSON";
                    //上传成功
                    data.success = function (response, statusText, xhr, $form) {
                        //response = eval("(" + response + ")");
                        fileBox.find(".uploading-label,.fileImgShow").remove();
                        $fileInput.appendTo(fileBox);
                        $form.remove();
                        $fileInput.hide();
                        if(response.status == 1){
                            $fileInput.hide();
                            fileBox.find("input[type='hidden']:first").val(response.data);
                            fileBox.append("<div class='media fileImgShow'><div class='media-left'><img height='80px' src='"+response.data+"'></div><div class='media-body media-bottom'><a>修改</a></div></div>")
                        }else{
                            fileBox.append("<label>上傳失敗，請刷新頁面</label>")
                        }
                    };

                    try {
                        //开始ajax提交表单
                        $form.ajaxSubmit(data);
                    } catch (e) {
                        alert(e.message);
                    }
                });
            });
        }
    });
})(jQuery)

