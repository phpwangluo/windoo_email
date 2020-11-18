<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Diy\ImportContactsAction;
use App\Admin\Actions\Diy\ChangeTaskStatusAction;
use App\Models\Contact;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ContactController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '邮件发送联系人管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Contact());
        $grid->model()->where('status', '=', 1);
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->equal('country_id', '国家')->select('/api/countrylist');
            $filter->equal('trade_id', '行业')->select('/api/tradelist');
            $filter->equal('send_count', '发送次数')->integer();

        });
        $grid->disableExport();//禁用导出
        //$grid->disableCreateButton(); //禁用创建
        //$grid->column('id', __('Id'));
        $grid->column('email_address', __('目标邮箱'));
        $grid->column('country.country_name', __('国家'));
        $grid->column('trade.trade_name', __('行业'));
        $grid->column('template.template_name', __('模板名称'));
        $grid->column('customer_tag', __('示例项目'));
        //$grid->column('send_start_hour', __('Send start hour'));
        //$grid->column('send_end_hour', __('Send end hour'));
        $grid->column('send_count', __('发送次数'));
        //$grid->column('business_status', __('Business status'));
        $grid->column('task_status', __('任务状态'))->using([
            0 => '停用',
            1 => '使用中',
        ], '未知')->dot([
            0 => 'danger',
            1 => 'success',
        ], 'warning');
        //$grid->column('status', __('Status'));
        //$grid->column('remarks', __('Remarks'));
        //$grid->column('created_at', __('Created at'));
        //$grid->column('updated_at', __('Updated at'));
        $grid->actions(function ($actions) {

            // 去掉删除
            $actions->disableDelete();

            // 去掉编辑
            //$actions->disableEdit();

            // 去掉查看
            $actions->disableView();

            // 添加自定义修改任务状态的按钮
            $actions->add(new ChangeTaskStatusAction());
        });
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        //导入
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new ImportContactsAction());
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
        $show = new Show(Contact::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('email_address', __('Email address'));
        $show->field('country_id', __('Country id'));
        $show->field('trade_id', __('Trade id'));
        $show->field('template_id', __('Template id'));
        $show->field('customer_tag', __('Customer tag'));
        $show->field('send_start_hour', __('Send start hour'));
        $show->field('send_end_hour', __('Send end hour'));
        $show->field('send_count', __('Send count'));
        $show->field('business_status', __('Business status'));
        $show->field('task_status', __('Task status'));
        $show->field('status', __('Status'));
        $show->field('remarks', __('Remarks'));
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
        $form = new Form(new Contact());

        $form->text('email_address', __('Email address'));
        $form->number('country_id', __('Country id'));
        $form->number('trade_id', __('Trade id'));
        $form->number('template_id', __('Template id'));
        $form->text('customer_tag', __('Customer tag'));
        $form->switch('send_start_hour', __('Send start hour'))->default(9);
        $form->switch('send_end_hour', __('Send end hour'))->default(17);
        $form->number('send_count', __('Send count'));
        $form->switch('business_status', __('Business status'))->default(1);
        $form->switch('task_status', __('Task status'))->default(1);
        $form->switch('status', __('Status'))->default(1);
        $form->text('remarks', __('Remarks'));

        return $form;
    }
}
