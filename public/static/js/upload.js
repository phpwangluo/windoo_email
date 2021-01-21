<!-- 编辑器容器 -->
function uploadImage(obj){
    var image = $(obj)[0].files[0];
    if (!image) return false;
    var formData = new FormData();
    formData.append("file", image)
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
    if (!file) return false;
    var formData = new FormData();
    formData.append("file", file)
    $.ajax({
        url:"/api/uploadFiles",
        type:"post",
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success:function(res){
            if (res.error === 1){

                console.log(res.content)

                $("textarea[name='content']").val(res.content);
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


