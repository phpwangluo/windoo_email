/*$(function () {
    //边栏设置选择栏目
    $("#ischange").change(function() {
        var checkID = [];//定义一个空数组
        $("input[name='category_id[]']:checked").each(function(i){//把所有被选中的复选框的值存入数组
            checkID[i] =$(this).val();
        });
        var site_id = $('#ischange').attr('site_id');
        var article_id = $('#ischange').attr('article_id');
        $.ajax({
            url:"/api/blogs/addarticlecategories",
            type:"post",
            data:{'category_ids':checkID,'site_id':site_id,'article_id':article_id},
            success:function(res){
                if (res.error === 1){
                    toastr.success(res.message)
                } else {
                    toastr.error(res.message)
                }
            },
            error:function(e){
                toastr.error("网络错误，请重试！！");
            }
        });
    });
});*/

function add_article_to_category(obj) {
    var checkID = [];//定义一个空数组
    $(obj).find("input[name='category_id[]']:checked").each(function(i){//把所有被选中的复选框的值存入数组
        checkID[i] =$(this).val();
    });
    var site_id = $(obj).attr('site_id');
    var article_id = $(obj).attr('article_id');
    $.ajax({
        url:"/api/blogs/addarticlecategories",
        type:"post",
        data:{'category_ids':checkID,'site_id':site_id,'article_id':article_id},
        success:function(res){
            if (res.error === 1){
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
//init.invokeAll();
