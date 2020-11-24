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
    protected $title = '邮箱服务器guanli';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MailSetting());

        $grid->column('id', __('Id'));
        $grid->column('driver', __('Driver'));
        $grid->column('host', __('Host'));
        $grid->column('port', __('Port'));
        $grid->column('encryption', __('Encryption'));
        $grid->column('sendmail', __('Sendmail'));
        $grid->column('pretend', __('Pretend'));
        $grid->column('support_name', __('Support_name'));
        $grid->column('getmail_protocol', __('Getmail_protocol'));
        $grid->column('getmail_host', __('Getmail_host'));
        $grid->column('getmail_port', __('Getmail_port'));
        $grid->column('getmail_encryption', __('Getmail_encryption'));
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

        $form->text('driver', __('Driver'));
        $form->text('host', __('Host'));
        $form->number('port', __('Port'));
        $form->text('encryption', __('Encryption'));
        $form->text('sendmail', __('Sendmail'));
        $form->switch('pretend', __('Pretend'));
        $form->text('support_name', __('Support_name'));
        $form->text('getmail_protocol', __('Getmail_protocol'));
        $form->text('getmail_host', __('Getmail_host'));
        $form->number('getmail_port', __('Getmail_port'));
        $form->text('Getmail_encryption', __('Getmail_encryption'));
        return $form;
    }
}
