<!-- 编辑器容器 -->
function uploadImage(obj){
    var image = $(obj)[0].files[0];
    var image_folder = $("input[name='image_folder']").val();
    if (!image) return false;
    if (!image_folder){
        image_folder = 'site_image';
    }
    var formData = new FormData();
    formData.append("file", image)
    formData.append("image_folder", image_folder)
    $.ajax({
        url:"/api/uploadImg",
        type:"post",
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success:function(res){
            if (res.error === 1){
                var input_name = $(obj).attr('input_name');
                $("input[name="+input_name+"]").attr("value",res.url);
                $(obj).prev(".add_image i").remove();
                $(obj).prev(".add_image img").remove();
                $(obj).prev(".add_image").html('<img src="/storage/upload/'+image_folder+'/'+res.url+'">');
                toastr.success(res.message)
            } else {
                toastr.error(res.message)
            }
        },
        error:function(e){
            toastr.error("网络错误，请重试！！");
        }
    });
}

function uploadFiles(obj) {
    var file = $(obj)[0].files[0];
    var image_folder = $("input[name='image_folder']").val();
    if (!file) return false;
    var formData = new FormData();
    formData.append("file", file)
    formData.append("image_folder", image_folder)
    $.ajax({
        url:"/api/uploadFiles",
        type:"post",
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success:function(res){
            if (res.error === 1){
                UE.delEditor("content");
                var ue = UE.getEditor('content');
                var site_id = $("input[name='site_id']").val();
                ue.addListener("ready", function () {
                    ue.execCommand('serverparam', {
                        'site_id':site_id,
                    });
                    ue.setContent(res.content, false);
                    ue.addListener("blur",function(){
                        var old_str = ue.getContent()
                        var imgReg = /<img.*?(?:>|\/>)/gi;
                        var srcReg = /src=[\'\"]?([^\'\"]*)[\'\"]?/i;
                        var arr = old_str.match(imgReg);  // arr 为包含所有img标签的数组
                        imgss = '';
                        for (var i = 0; i < arr.length; i++) {
                            var src = arr[i].match(srcReg);
                            //获取图片地址
                            var arr_src=src[1].split("/");
                            var end = arr_src.pop()
                            imgss +='<img class="avatar" onclick="choosePhoto(this)" data="'+end+'" src="'+src[1]+'">'
                        }
                        $('#add_article_image').html(imgss);
                        return;
                    });
                });
                var imgs = '';
                $.each(res.image_lists, function(key, val) {
                    var arr_src1=val.split("/");
                    var end1 = arr_src1.pop()
                    imgs +='<img class="avatar" onclick="choosePhoto(this)" data="'+end1+'" src="'+val+'">'
                });
                $('#add_article_image').html(imgs);
                toastr.success(res.message)
            } else {
                toastr.error(res.message)
            }
        },
        error:function(e){
            toastr.error("网络错误，请重试！！");
        }
    });
}

function choosePhoto(obj) {
    $(obj).css("border","2px solid red").siblings().css("border","2px solid #cccccc");
    var article_photo = $(obj).attr('data');
    $("input[name='photo']").val(article_photo)
}



