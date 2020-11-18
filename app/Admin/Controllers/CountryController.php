<?php

namespace App\Admin\Controllers;

use App\Models\Country;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CountryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '国家管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Country());
        $grid->disableCreateButton(); //禁用添加
        $grid->disableFilter();//禁用查询
        $grid->disableExport();//禁用导出
        //$grid->column('id', __('Id'));
        $grid->column('country_name', __('国家'));
        $grid->column('send_start_hour', __('开始时间'));
        $grid->column('send_end_hour', __('结束时间'));
        //$grid->column('created_at', __('Created at'));
        //$grid->column('updated_at', __('Updated at'));

        $grid->actions(function ($actions) {

            // 去掉删除
            $actions->disableDelete();

            // 去掉编辑
            //$actions->disableEdit();

            // 去掉查看
            $actions->disableView();
        });

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
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
        $show = new Show(Country::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('country_name', __('Country name'));
        $show->field('send_start_hour', __('Start'));
        $show->field('send_end_hour', __('End'));
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
        $form = new Form(new Country());

        $form->text('country_name', __('国家'))->readonly()->required();
        $form->number('send_start_hour', __('开始时间'))->required()->min(0)->max(23);
        $form->number('send_end_hour', __('结束时间'))->required()->min(0)->max(23);
        $form->tools(function (Form\Tools $tools) {

            // 去掉`列表`按钮
            //$tools->disableList();

            // 去掉`删除`按钮
            $tools->disableDelete();

            // 去掉`查看`按钮
            $tools->disableView();

        });
        $form->footer(function ($footer) {

            // 去掉`重置`按钮
            //$footer->disableReset();

            // 去掉`提交`按钮
            //$footer->disableSubmit();

            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();

            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();

        });
        return $form;
    }
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // 从$model取出数据并进行处理

        });
    }
}
