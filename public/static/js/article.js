$(function () {
    var site_id = $("input[name='site_id']").val();
    var ue = UE.getEditor('content');
    ue.addListener("ready", function () {
        ue.execCommand('serverparam', {
            'site_id':site_id,
        });
        ue.addListener("blur",function(){
            var str = ue.getContent()
            var imgReg = /<img.*?(?:>|\/>)/gi;
            var srcReg = /src=[\'\"]?([^\'\"]*)[\'\"]?/i;
            var arr = str.match(imgReg);  // arr 为包含所有img标签的数组
            imgs = '';
            for (var i = 0; i < arr.length; i++) {
                var src = arr[i].match(srcReg);
                var arr_src=src[1].split("/");
                var end = arr_src.pop()
                //获取图片地址
                imgs +='<img class="avatar" onclick="choosePhoto(this)" data="'+end+'" src="'+src[1]+'">'
            }
            $('#add_article_image').html(imgs);
        });
    });
});
//init.invokeAll();
