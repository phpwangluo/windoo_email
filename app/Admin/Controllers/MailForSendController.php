<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use \App\Models\MailForSend;

class MailForSendController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '邮件发送记录';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MailForSend());
        $grid->disableFilter();//禁用查询
        $grid->disableExport();//禁用导出

        $grid->column('id', __('Id'));
        $grid->column('sender_email', __('发件箱地址'));
        $grid->column('receiver_email', __('收件箱地址'));
        $grid->column('title', __('发送标题'));
        //$grid->column('email_sign', __('发送签名'));
        //$grid->column('content', __('Content'));
        //$grid->column('template_id', __('Template id'));
        $grid->column('send_type', __('发送类型'));
        //$grid->column('send_start_hour', __('Send start hour'));
        //$grid->column('send_end_hour', __('Send end hour'));
        //$grid->column('send_max_num', __('Send max num'));
        $grid->column('sender_local_time', __('收件人当地时间'));
        $grid->column('send_status', __('发送状态'));
        //$grid->column('created_at', __('Created at'));
        //$grid->column('updated_at', __('Updated at'));
        $grid->column('plan_send_time', __('服务器计划发送时间'));
        $grid->column('real_send_time', __('邮件实际发送时间'));

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
        $show = new Show(MailForSend::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('sender_email', __('Sender email'));
        $show->field('receiver_email', __('Receiver email'));
        $show->field('title', __('Title'));
        $show->field('email_sign', __('Email sign'));
        $show->field('content', __('Content'));
        $show->field('template_id', __('Template id'));
        $show->field('send_type', __('Send type'));
        $show->field('send_start_hour', __('Send start hour'));
        $show->field('send_end_hour', __('Send end hour'));
        $show->field('send_max_num', __('Send max num'));
        $show->field('sender_local_time', __('Sender local time'));
        $show->field('send_status', __('Send status'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('plan_send_time', __('Plan send time'));
        $show->field('real_send_time', __('Real send time'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MailForSend());

        $form->text('sender_email', __('Sender email'));
        $form->text('receiver_email', __('Receiver email'));
        $form->text('title', __('Title'));
        $form->text('email_sign', __('Email sign'));
        $form->textarea('content', __('Content'));
        $form->number('template_id', __('Template id'));
        $form->switch('send_type', __('Send type'))->default(1);
        $form->switch('send_start_hour', __('Send start hour'))->default(9);
        $form->switch('send_end_hour', __('Send end hour'))->default(17);
        $form->switch('send_max_num', __('Send max num'))->default(5);
        $form->datetime('sender_local_time', __('Sender local time'))->default(date('Y-m-d H:i:s'));
        $form->switch('send_status', __('Send status'))->default(1);
        $form->datetime('plan_send_time', __('Plan send time'))->default(date('Y-m-d H:i:s'));
        $form->datetime('real_send_time', __('Real send time'))->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
