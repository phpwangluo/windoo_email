<!-- 编辑器容器 -->
function uploadImage(obj){
    var image = $(obj)[0].files[0];
    var image_folder = $("input[name='image_folder']").val();
    if (!image) return false;
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
                $(obj).prev(".add_image").html('<img src="'+res.url+'">');
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
                ue.addListener("ready", function () {
                    ue.setContent(res.content, false);
                    ue.addListener("blur",function(){
                        var old_str = ue.getContent()
                        console.log(ss)

                        $('#add_article_image').html(imgss);
                        return;
                    });
                });
                var imgs = '';
                $.each(res.image_lists, function(key, val) {
                    imgs +='<img class="avatar" onclick="choosePhoto(this)" src="'+val+'">'
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
    var article_photo = $(obj).attr('src');
    $("input[name='photo']").val(article_photo)
}



