<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Diy\NewDelete;
use App\Admin\Actions\Diy\ImportSenderAction;
use App\Models\Sender;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SenderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '发件箱管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Sender());
        $grid->model()->where('status', '=', 1);
        $grid->disableFilter();//禁用查询
        $grid->disableExport();//禁用导出
        $grid->disableCreateButton(); //禁用创建
        //$grid->column('id', __('Id'));
        $grid->column('mailsetting.support_name', __('运营商'));
        $grid->column('email_address', __('邮箱名称'));
        //$grid->column('email_pass', __('Email pass'));
        //$grid->column('email_sign', __('Email sign'));
        $grid->column('send_count', __('发送次数'))->sortable()->totalRow();
        $grid->column('receive_count', __('回收次数'));
        //$grid->column('max_send_count', __('Max send count'));
        $grid->column('email_status', __('状态'))->using([
            0 => '停用',
            1 => '使用中',
        ], '未知')->dot([
            0 => 'danger',
            1 => 'success',
        ], 'warning');
        //$grid->column('status', __('Status'));
        $grid->column('remarks', __('备注'));
        //$grid->column('created_at', __('Created at'));
        //$grid->column('updated_at', __('Updated at'));
        $grid->actions(function ($actions) {

            // 去掉删除
            $actions->disableDelete();

            // 去掉编辑
            //$actions->disableEdit();

            // 去掉查看
            $actions->disableView();

            // 添加自定义删除按钮
            $actions->add(new NewDelete());
        });
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        //导入
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new ImportSenderAction());
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
        $show = new Show(Sender::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('email_address', __('Email address'));
        $show->field('email_pass', __('Email pass'));
        $show->field('email_sign', __('Email sign'));
        $show->field('send_count', __('Send count'));
        $show->field('receive_count', __('Receive count'));
        $show->field('max_send_count', __('Max send count'));
        $show->field('email_status', __('Email status'));
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
        $form = new Form(new Sender());
        $form->tools(function (Form\Tools $tools) {

            // 去掉`列表`按钮
            //$tools->disableList();

            // 去掉`删除`按钮
            $tools->disableDelete();
            // 去掉`查看`按钮
            $tools->disableView();

        });
        $form->text('mailsetting.support_name', __('运营商'))->readonly();
        $form->text('email_address', __('邮箱名称'))->readonly();
        $form->text('email_pass', __('邮箱密码'));
        //$form->text('email_sign', __('Email sign'));
        //$form->number('send_count', __('Send count'));
        //$form->number('receive_count', __('Receive count'));
        //$form->number('max_send_count', __('Max send count'))->default(1000);
        //$form->switch('email_status', __('状态'))->default(1);

        $states = [
            'on'  => ['value' => 1, 'text' => '启用', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
        ];
        $form->switch('email_status', __('状态'))->states($states)->default(1);
        //$form->switch('status', __('Status'))->default(1);
        $form->text('remarks', __('备注'));
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
