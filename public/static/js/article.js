$(function () {
    var ue = UE.getEditor('content');
    ue.addListener("ready", function () {
        ue.addListener("blur",function(){
            var str = ue.getContent()
            console.log(str)
            var s = str.match(/<img\s*src=\"([^\"]*?)\"[^>]*>/gi);
            var imgs = '';
            for (var i = 0; i < s.length; i++) {
                s2=s[i].split(/\<img\s*src=\"([^\"]*?)\"[^>]*>/gi);
                src = s2[1].replace('http://www.windoo_email.com', '')
                imgs +='<img class="avatar" onclick="choosePhoto(this)" src="'+src+'">'
            }
            console.log(imgs)
            $('#add_article_image').html(imgs);
        });
    });
});
//init.invokeAll();