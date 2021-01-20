<?php

namespace App\Admin\Controllers\Sites;

use App\Models\SitesBlogAuthors;
use App\Models\SitesBlogCategories;
use App\Models\SitesBlogPageSettings;
use App\Models\SitesBlogSites;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Admin;
use Illuminate\Support\MessageBag;

class BlogSitesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '站点创建';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SitesBlogSites());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('type', __('Type'));
        $grid->column('industry', __('Industry'));
        $grid->column('domain_name', __('Domain name'));
        $grid->column('logo', __('Logo'));
        $grid->column('photo', __('Photo'));
        $grid->column('contact', __('Contact'));
        $grid->column('address', __('Address'));
        $grid->column('publish_time', __('Publish time'));
        $grid->column('date_format', __('Date format'));
        $grid->column('detail_title', __('Detail title'));
        $grid->column('status', __('Status'));
        $grid->column('remark', __('Remark'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(SitesBlogSites::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('type', __('Type'));
        $show->field('industry', __('Industry'));
        $show->field('domain_name', __('Domain name'));
        $show->field('logo', __('Logo'));
        $show->field('photo', __('Photo'));
        $show->field('contact', __('Contact'));
        $show->field('address', __('Address'));
        $show->field('publish_time', __('Publish time'));
        $show->field('date_format', __('Date format'));
        $show->field('detail_title', __('Detail title'));
        $show->field('status', __('Status'));
        $show->field('remark', __('Remark'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

        $form = new Form(new SitesBlogSites());
        $form->tools(function (Form\Tools $tools) {

            // 去掉`列表`按钮
            //$tools->disableList();
            // 去掉`删除`按钮
            $tools->disableDelete();
            // 去掉`查看`按钮
            $tools->disableView();

        });
        $form->divider('站点信息');
        $form->text('name', __('站点名称'))->required();
        $form->text('domain_name', __('站点域名'))->required();
        $form->text('industry', __('行业'))->required();
        $form->image('logo', __('Logo'))->thumbnail('small', $width = 300, $height = 300);
        $form->image('photo', __('头图'))->thumbnail('small', $width = 300, $height = 300);
        $form->select('type', __('站点类型'))->options([
            'BLOG'=>'博客',
            'COMMERCE'=>'商品'
        ])->required();
        $form->tags('categories_names', __('站点分类'))
            ->required()
            ->placeholder('使用英文逗号分割，可以一次添加多个！');
        $form->select('detail_title', __('Title风格'))->options([
            0=>'Article Title - SiteName',
            1=>'Article Title - Site Name',
            10=>'Article Title | SiteName',
            11=>'Article Title | Site Name'
        ])->required();
       // $form->number('per_page_nums', __('分页数'))->required()->default(10);
        $form->text('remark', __('备注'));

        $form->divider('博主信息');
        $form->text('first_name', __('First Name'))->required();
        $form->text('last_name', __('Last Name'))->required();
        //$form->text('profile', __('博主简介'))->required();
        $form->UEditor('profile', __('博主简介'));

        $form->image('author_photo', __('博主头像'))->thumbnail('small', $width = 300, $height = 300);
        // 第一列占据1/2的页面宽度
        //$form->setWidth(9, 3);
        /*$form->column(1/2, function ($form) {
            $form->text('name', __('站点名称'))->required();
            $form->text('domain_name', __('站点域名'))->required();
            $form->text('industry', __('行业'))->required();
            $form->image('logo', __('Logo'))->thumbnail('small', $width = 300, $height = 300);
            $form->image('photo', __('头图'))->thumbnail('small', $width = 300, $height = 300);

        });
        $form->column(1/2, function ($form) {
            $form->select('type', __('站点类型'))->options([
                0=>'博客',
                1=>'商品'
            ])->required();
            $form->tags('categories_name', __('站点分类'))
                ->required()
                ->placeholder('使用英文逗号分割，可以一次添加多个！');
            $form->select('page_title_type', __('Title风格'))->options([
                0=>'Article Title - SiteName',
                1=>'Article Title - Site Name',
                10=>'Article Title | SiteName',
                11=>'Article Title | Site Name'
            ])->required();
            $form->number('per_page_nums', __('分页数'))->required()->default(10);
            $form->text('remark', __('备注'));
        });
        */
        $form->saving(function ($model) {
            //添加站点基本信息
            $insert_sites = [
                'name'=>$model->name,
                'type'=>$model->type,
                'industry'=>$model->industry,
                'domain_name'=>$model->domain_name,
                //'logo'=>$model->logo,
                //'photo'=>$model->photo,
                'detail_title'=>$model->detail_title
            ];
            $site_id = SitesBlogSites::insertGetId($insert_sites);
            if ($site_id){
                //添加页面信息
                //随机获取分类uri前缀
                $category_uri_prefix_arr = Config()->get('blogs.category_prefix');
                $rand_key =  array_rand($category_uri_prefix_arr,1);
                $category_uri_prefix = $category_uri_prefix_arr[$rand_key];
                //组装操作数组
                $insert_page_setting = [
                    'Home'=>[
                        'page_type'=>1,
                        'uri'=>'/',
                        'site_id'=>$site_id
                    ],
                    'About us'=>[
                        'page_type'=>2,
                        'uri'=>'/about-us/',
                        'site_id'=>$site_id
                    ],
                    'Privacy Policy'=>[
                        'page_type'=>3,
                        'uri'=>'/privacy-policy/',
                        'site_id'=>$site_id
                    ],
                    'Contact'=>[
                        'page_type'=>4,
                        'uri'=>'/guest-message/',
                        'site_id'=>$site_id
                    ]
                ];
                $categories_arr = $model->categories_names;
                foreach ($categories_arr as $cate_name){
                    if($cate_name && $cate_name != ''){
                        $site_name_arr = explode(' ', $cate_name);
                        $words_cate = [];
                        $words_uri = [];
                        foreach ($site_name_arr as $key=>$s) {
                            $words_cate[$key]=ucfirst($s);
                            $words_uri[$key]=strtolower($s);
                        }
                        $cate = implode(' ', $words_cate);
                        $uri = implode('-', $words_uri);
                        $insert_page_setting[$cate]['site_id'] = $site_id;
                        $insert_page_setting[$cate]['page_type'] = 5;
                        $insert_page_setting[$cate]['uri'] = $category_uri_prefix.'/'.$uri.'/';
                    }
                }
                foreach ($insert_page_setting as $name=> $page_data){
                    $page_id = SitesBlogPageSettings::insertGetId($page_data);
                    if ($page_id){
                        //同步到categories表
                        $insert_category = [
                            'page_id'=>$page_id,
                            'name'=>$name,
                            'type'=> $page_data['page_type'] == 5 ? 1 :0,
                            'site_id'=>$site_id
                        ];
                        $cate_id = SitesBlogCategories::insertGetId($insert_category);
                    }
                }
                //添加博主信息
                $insert_author = [
                    'first_name'=>ucfirst($model->first_name),
                    'last_name'=>ucfirst($model->last_name),
                    //'photo'=>$model->author_photo,
                    'site_id'=>$site_id,
                ];
                if ($model->profile){
                    $insert_author['profile'] = $model->profile;
                }
                $site_id = SitesBlogAuthors::insertGetId($insert_author);
            }
            $success = new MessageBag([
                'title'   => '提示',
                'message' => '回复成功',
            ]);
            return redirect(url("admin/sites-blog-sites"))->with(compact('success'));

        });
/*
        //dd($form->model()->php_errormsg);
        Admin::js('vendor/laravel-admin/sweetalert2/dist/sweetalert2.min.js');
        $script = <<<SCRIPT
$('button[type=submit]').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    swal({
        title: "创建成功",
        text: "{$form->model()->name}",
        type: "success",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "确定",
        cancelButtonText: "录入文章",
        closeOnConfirm: false
    }, function(isConfirm){
        if (isConfirm) form.submit();
    });
});
SCRIPT;

        Admin::script($script);*/
        $form->footer(function ($footer) {

            // 去掉`重置`按钮
            //$footer->disableReset();

            // 去掉`提交`按钮
            //$footer->disableSubmit();
            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            //$footer->disableEditingCheck();

            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();

        });
        return $form;
    }
}
