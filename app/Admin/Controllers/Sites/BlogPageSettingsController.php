<?php

namespace App\Admin\Controllers\Sites;

use Encore\Admin\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use \App\Models\SitesBlogPageSettings;

class BlogPageSettingsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '页面设置';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SitesBlogPageSettings());
        $obj = $grid->model();
        if(request('site_id')) {
            $obj->where('site_id','=',request('site_id'));
        }
        $obj->orderBy('site_id','desc');
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->equal('site_id', '站点ID');
            $filter->equal('sites.name', '站点名称');
            $filter->equal('title', '文章标题');

        });
        $grid->disableExport();//禁用导出
        $grid->disableCreateButton(); //禁用创建
        $grid->fixColumns(1,-1);
        $grid->column('site_id', __('站点ID'));
        $grid->column('sites.name', __('站点名称'));
        $grid->column('categories.name', __('分类名称'));
        $grid->column('photo', __('页面图标'))->image();
        $grid->column('page_title', __('页面标题'))->limit(30,'...');
        $grid->column('page_description', __('页面描述'))->limit(30,'...');
        $grid->column('page_keywords', __('页面关键字'))->limit(30,'...');
        $grid->actions(function ($actions) {

            // 去掉删除
            $actions->disableDelete();

            // 去掉编辑
            //$actions->disableEdit();

            // 去掉查看
            $actions->disableView();
            // 添加自定义查看的按钮
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
        $show = new Show(SitesBlogPageSettings::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('page_type', __('Page type'));
        $show->field('content', __('Content'));
        $show->field('abstract', __('Abstract'));
        $show->field('photo', __('Photo'));
        $show->field('uri', __('Uri'));
        $show->field('page_title', __('Page title'));
        $show->field('page_description', __('Page description'));
        $show->field('page_keywords', __('Page keywords'));
        $show->field('site_id', __('Site id'));
        $show->field('status', __('Status'));
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
        $form = new Form(new SitesBlogPageSettings());
        $id=request()->route()->parameters()['sites_blog_page_setting'];
        $page_type = SitesBlogPageSettings::query()->where('id','=',$id)->first();

        $form->tools(function (Form\Tools $tools) {

            // 去掉`列表`按钮
            //$tools->disableList();
            // 去掉`删除`按钮
            $tools->disableDelete();
            // 去掉`查看`按钮
            $tools->disableView();

        });
        if($page_type->page_type == 2 || $page_type->page_type == 3){
            $form->UEditor('content', __('页面内容'))->required();
        }else{
            $form->number('site_id', __('站点ID'))->disable();
            $form->number('sites.name', __('站点名称'))->disable();
            Admin::css('/static/css/upload.css');
            Admin::js('/static/js/upload.js');
            $form->display('photo', '分类图标')->with(function ($value) {
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
            $form->text('page_title', __('页面标题'));
            $form->textarea('page_description', __('页面描述'));
            $form->text('page_keywords', __('关键字'));
        }
        //$form->text('abstract', __('Abstract'));

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
