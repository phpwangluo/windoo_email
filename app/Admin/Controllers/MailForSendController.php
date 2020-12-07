<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DiyHandle\ChangeMailForSendStatus;
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
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('receiver_email', '联系人')->email();
            $filter->equal('send_status', '发送状态')->select([
                '1'=>'待发送',
                '2'=>'已发送',
                '3'=>'已取消'
            ]);
            $filter->equal('send_type', '发送类型')->select([
                '1'=>'自动',
                '2'=>'手动',
            ]);
        });
        $grid->disableExport();//禁用导出
        $grid->disableCreateButton();
        $grid->column('id', __('Id'))->hide();
        $grid->column('sender_email', __('发件人'))->hide();
        $grid->column('receiver_email', __('联系人'))->filter();
        $grid->column('title', __('标题'));
        //$grid->column('email_sign', __('发送签名'));
        //$grid->column('content', __('Content'));
        //$grid->column('template_id', __('Template id'));
        $grid->column('send_type', __('发送类型'))->using([
            1 => '自动',
            2 => '手动',
        ], '未知')->label([
            1 => 'info',
            2 => 'info',
        ], 'warning');
        //$grid->column('send_start_hour', __('Send start hour'));
        //$grid->column('send_end_hour', __('Send end hour'));
        //$grid->column('send_max_num', __('Send max num'));
        $grid->column('sender_local_time', __('收件人当地时间'))->hide();
        $grid->column('send_status', __('发送状态'))->using([
            1 => '待发送',
            2 => '已发送',
            3 => '已取消',
        ], '未知')->dot([
            1 => 'warning',
            2 => 'success',
            3 => 'info',
        ], 'warning');
        //$grid->column('created_at', __('Created at'));
        //$grid->column('updated_at', __('Updated at'));
        $grid->column('plan_send_time', __('计划发送时间'))->sortable();
        $grid->column('real_send_time', __('实际发送时间'))->sortable();
        $grid->actions(function ($actions) {

            // 去掉删除
            $actions->disableDelete();

            // 去掉编辑
            $actions->disableEdit();

            // 去掉查看
            $actions->disableView();

            $actions->prepend('<a
                    title="查看"
                    href="'.$this->getResource().'/'.$this->getRouteKey().'"
                    class="'.$this->grid->getGridRowName().'-view">
                    <i class="fa fa-eye"></i>
                </a>&nbsp;&nbsp;');
            // 添加自定义删除按钮
            if($actions->row->send_status != 2){
                if($actions->row->send_status == 1){
                    $name = '取消发送';
                    $to_send_status = 3;
                }else{
                    $name = '恢复发送';
                    $to_send_status = 1;
                }
                // 添加自定义修改任务状态的按钮
                //$actions->add(new ChangeSendStatusAction($name));
                // 老版本添加自定义删除按钮
                $actions->append(new ChangeMailForSendStatus($actions->getKey(),$to_send_status));
            }
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
        $show = new Show(MailForSend::findOrFail($id));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                //$tools->disableList();
                $tools->disableDelete();
            });;
        $show->field('sender_email', __('发件人'));
        $show->field('receiver_email', __('联系人'));
        $show->field('title', __('标题'));
        $show->field('email_sign', __('签名'));
        $show->content('正文')->unescape()->mailforsendcontent($id);
        $show->field('template.template_name', __('邮件模板'));
        $show->field('send_type', __('发送类型'))->using([1=> '自动', 2 => '手动']);
        $show->field('sender_local_time', __('当地邮件时间'));
        $show->field('send_status', __('发送状态'))->using([1=> '待发送', 2 => '已发送',3 => '已取消']);
        $show->field('plan_send_time', __('计划发送时间'));
        $show->field('real_send_time', __('实际发送时间'));
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
