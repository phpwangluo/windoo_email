<?php

namespace App\Admin\Controllers;

use App\Models\MailSetting;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class MailSettingController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '收发邮箱服务器管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MailSetting());
        $grid->column('id', __('编号'));
        $grid->column('driver', __('发送Driver'));
        $grid->column('host', __('发送Host'));
        $grid->column('port', __('发送Port'));
        $grid->column('encryption', __('发送Encryption'));
        //$grid->column('sendmail', __('发送Sendmail'));
        //$grid->column('pretend', __('发送Pretend'));
        $grid->column('support_name', __('运营商'));
        $grid->column('getmail_protocol', __('拉取protocol'));
        $grid->column('getmail_host', __('拉取host'));
        $grid->column('getmail_port', __('拉取port'));
        $grid->column('getmail_encryption', __('拉取encryption'));
        $grid->actions(function ($actions) {

            // 去掉删除
            $actions->disableDelete();

            // 去掉编辑
            $actions->disableEdit();

            // 去掉查看
            $actions->disableView();

            $actions->prepend('<a
                title="编辑"
                href="'.$this->getResource().'/'.$this->getRouteKey().'/edit"
                class="'.$this->grid->getGridRowName().'-edit">
                <i class="fa fa-edit"></i>&nbsp;&nbsp;');
        });
        //$grid->column('created_at', __('Created at'));
        //$grid->column('updated_at', __('Updated at'));

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
        $show = new Show(MailSetting::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('driver', __('Driver'));
        $show->field('host', __('Host'));
        $show->field('port', __('Port'));
        $show->field('encryption', __('Encryption'));
        $show->field('sendmail', __('Sendmail'));
        $show->field('pretend', __('Pretend'));
        $show->field('support_name', __('Support_name'));
        $show->field('getmail_protocol', __('Getmail_protocol'));
        $show->field('getmail_host', __('Getmail_host'));
        $show->field('getmail_port', __('Getmail_port'));
        $show->field('getmail_encryption', __('Getmail_encryption'));
        //$show->field('created_at', __('Created at'));
        //$show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MailSetting());
        $form->tools(function (Form\Tools $tools) {

            // 去掉`列表`按钮
            //$tools->disableList();

            // 去掉`删除`按钮
            $tools->disableDelete();
            // 去掉`查看`按钮
            $tools->disableView();

        });
        $form->text('driver', __('发送Driver'))->required();
        $form->text('host', __('发送Host'))->required();
        $form->number('port', __('发送Port'))->required();
        $form->text('encryption', __('发送Encryption'))->required();
        $form->text('sendmail', __('发送Sendmail'));
        $form->switch('pretend', __('发送Pretend'));
        $form->text('support_name', __('运营商'))->required();
        $form->text('getmail_protocol', __('拉取protocol'))->required();
        $form->text('getmail_host', __('拉取host'))->required();
        $form->number('getmail_port', __('拉取port'))->required();
        $form->text('Getmail_encryption', __('拉取encryption'))->required();
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
