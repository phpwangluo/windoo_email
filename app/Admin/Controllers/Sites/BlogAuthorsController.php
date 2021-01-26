<?php

namespace App\Admin\Controllers\Sites;

use Encore\Admin\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use \App\Models\SitesBlogAuthors;

class BlogAuthorsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '博主管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SitesBlogAuthors());

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

        });
        $grid->disableExport();//禁用导出
        $grid->disableCreateButton(); //禁用创建
        $grid->column('site_id', __('站点ID'));
        $grid->column('sites.name', __('站点名称'));
        $grid->column('author_name', __('博主名称'))->display(function () {
            return $this->first_name.' '.$this->last_name;
        });
        $grid->column('photo', __('博主头像'))->image();
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
        $show = new Show(SitesBlogAuthors::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('type', __('Type'));
        $show->field('first_name', __('First name'));
        $show->field('last_name', __('Last name'));
        $show->field('profile', __('Profile'));
        $show->field('email', __('Email'));
        $show->field('photo', __('Photo'));
        $show->field('home_page', __('Home page'));
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
        $form = new Form(new SitesBlogAuthors());
        $form->tools(function (Form\Tools $tools) {

            // 去掉`列表`按钮
            //$tools->disableList();
            // 去掉`删除`按钮
            $tools->disableDelete();
            // 去掉`查看`按钮
            $tools->disableView();

        });
        $form->text('first_name', __('First name'))->required();
        $form->text('last_name', __('Last name'))->required();
        $form->UEditor('profile', __('简介'))->required();
        Admin::css('/static/css/upload.css');
        Admin::js('/static/js/upload.js');
        $form->display('photo', '头像')->with(function ($value) {
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
