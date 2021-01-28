<?php

namespace App\Admin\Controllers\Sites;

use App\Admin\Actions\Diy\DiyAddButtonAction;
use App\Models\SitesBlogCategories;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BlogCategriesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '边栏管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SitesBlogCategories());
        $obj = $grid->model()
            ->where('type','=',2);
        if(request('site_id')) {
            $obj->where('site_id','=',request('site_id'));
        }
        $obj->orderBy('site_id','desc')
            ->orderBy('ordinal_number','asc');
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->equal('site_id', '站点ID');
            $filter->equal('sites.name', '站点名称');

        });
        $grid->disableExport();//禁用导出
        $grid->disableCreateButton(); //禁用创建
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new DiyAddButtonAction());
        });
        $grid->column('site_id', __('站点ID'));
        $grid->column('sites.name', __('站点名称'));
        $grid->column('name', __('边栏名称'));
        $grid->column('ordinal_number', __('排序'));
        $grid->actions(function ($actions) {

            // 去掉删除
            $actions->disableDelete();

            // 去掉编辑
            $actions->disableEdit();

            // 去掉查看
            $actions->disableView();
            // 添加自定义查看的按钮
            $actions->prepend('<a title="边栏设置" href="sites-blog-article-categories?site_id='.$actions->row->site_id.'"><i class="fa fa-link"></i></a>&nbsp;&nbsp;');
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
        $show = new Show(SitesBlogCategories::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('type', __('Type'));
        $show->field('page_id', __('Page id'));
        $show->field('ordinal_number', __('Ordinal number'));
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
        $form = new Form(new SitesBlogCategories());
        $form->tools(function (Form\Tools $tools) {

            // 去掉`列表`按钮
            //$tools->disableList();
            // 去掉`删除`按钮
            $tools->disableDelete();
            // 去掉`查看`按钮
            $tools->disableView();

        });
        $form->number('site_id', __('站点ID'))->default(request('site_id'))->required();
        $form->text('name', __('边栏名称'))->required();
        $form->hidden('type')->default(2);

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
