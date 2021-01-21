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
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            //$filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->equal('name', '站点名称');
            $filter->equal('domain_name', '站点域名');

        });
        $grid->disableExport();//禁用导出
        $grid->disableCreateButton(); //禁用创建
        $grid->fixColumns(1,-1);
        $grid->column('id', __('站点ID'));
        $grid->column('name', __('站点名称'));
        $grid->column('logo', __('站点Logo'))->image();
        $grid->column('type', __('站点类型'))->using([
            'BLOG'=>'博客',
            'COMMERCE'=>'商品'
        ], '未知');
        $grid->column('categories_name', __('站点分类'))->display(function () {
            $cate_name_arr = [];
            foreach ($this->categories as $item){
                if($item->type == 1){
                    $cate_name_arr[] =  $item->name;
                }
            }
            return implode('/',$cate_name_arr);
        });
        $grid->column('detail_title', __('文章标题'))->using([
            0=>'Article Title - SiteName',
            1=>'Article Title - Site Name',
            10=>'Article Title | SiteName',
            11=>'Article Title | Site Name'
        ], '未知');
        $grid->column('industry', __('行业'));
        $grid->column('domain_name', __('站点域名'));

        $grid->column('photo', __('站点头图'))->image();
        $grid->column('remark', __('备注'));
        $grid->column('author_name', __('博主名称'))->display(function () {
            return $this->blog_author->first_name.' '.$this->blog_author->last_name;
        });

        $grid->column('blog_author.photo', __('博主头像'))->image();


        $grid->actions(function ($actions) {

            // 去掉删除
            $actions->disableDelete();

            // 去掉编辑
            $actions->disableEdit();

            // 去掉查看
            $actions->disableView();
            // 添加自定义查看的按钮
            /*if($actions->row->receive_status == 1){
                $actions->add(new MailReceivedDetailAction());
                $actions->add(new ChangeBusinessStatusAction());
                $actions->add(new DoReplyByUserAction());
            }*/
            // prepend一个操作
            $actions->prepend('<a title="页面设置" href="'.$this->getResource().'/'.$actions->getKey().'"><i class="fa fa-list-alt"></i></a>&nbsp;&nbsp;');

            $actions->prepend('<a title="边栏设置" href="'.$this->getResource().'/'.$actions->getKey().'"><i class="fa fa-list-alt"></i></a>&nbsp;&nbsp;');

            $actions->prepend('<a title="文章管理" href="'.$this->getResource().'/'.$actions->getKey().'"><i class="fa fa-list-alt"></i></a>&nbsp;&nbsp;');

            $actions->prepend('<a title="文章录入" href="sites-blog-articles/create?site_id='.$actions->row->id.'"><i class="fa fa-list-alt"></i></a>&nbsp;&nbsp;');
        });
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
        $form->setTitle('站点创建');
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
        Admin::css('/static/css/upload.css');
        Admin::js('/static/js/upload.js');
        $form->display('logo', 'Logo')->with(function ($value) {
            $html = '<div class="bob-upload" tabindex="0"><div class="add_image">';
            if ($value) {
                $html .= '<img class="avatar" src="' . $value . '">';
            } else {
                $html .= '<i class="avatar-uploader-icon fa fa-plus"></i>';
            }
            $html .= '</div><input class="bob-upload-input" type="file"  name="file" input_name="logo" onchange="uploadImage(this)">';
            $html .= '<input type="text" class="avatar-uploader-icon" name="logo" style="display:none;"></div>';
            return $html;
        });
        $form->display('photo', '头图')->with(function ($value) {
            $html = '<div class="bob-upload" tabindex="0"><div class="add_image">';
            if ($value) {
                $html .= '<img class="avatar" src="' . $value . '">';
            } else {
                $html .= '<i class="avatar-uploader-icon fa fa-plus"></i>';
            }
            $html .= '</div><input class="bob-upload-input" type="file"  name="file" input_name="photo" onchange="uploadImage(this)">';
            $html .= '<input type="text" class="avatar-uploader-icon" name="photo" style="display:none;"></div>';
            return $html;
        });
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
        $form->display('author_photo', '博主头像')->with(function ($value) {
            $html = '<div class="bob-upload" tabindex="0"><div class="add_image">';
            if ($value) {
                $html .= '<img class="avatar" src="' . $value . '">';
            } else {
                $html .= '<i class="avatar-uploader-icon fa fa-plus"></i>';
            }
            $html .= '</div><input class="bob-upload-input" type="file"  name="file" input_name="author_photo" onchange="uploadImage(this)">';
            $html .= '<input type="text"  name="author_photo" style="display:none;"></div>';
            return $html;
        });
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
                'logo'=>$model->logo,
                'photo'=>$model->photo,
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
                    'photo'=>$model->author_photo,
                    'site_id'=>$site_id,
                ];
                if ($model->profile){
                    $insert_author['profile'] = $model->profile;
                }
                $site_id = SitesBlogAuthors::insertGetId($insert_author);
            }
            $success = new MessageBag([
                'title'   => '提示',
                'message' => '创建成功',
            ]);
            return redirect(url("admin/sites-blog-sites"))->with(compact('success'));

        });
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
