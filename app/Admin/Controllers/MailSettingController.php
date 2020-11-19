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

        return $form;
    }
}
