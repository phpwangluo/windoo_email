<?php

namespace App\Admin\Controllers;

use App\Models\BusinessSource;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BusinessSourceController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '合作资源管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new BusinessSource());

        $grid->column('id', __('Id'));
        $grid->column('email_address', __('Email address'));
        $grid->column('other_contact', __('Other contact'));
        $grid->column('country_id', __('Country id'));
        $grid->column('trade_id', __('Trade id'));
        $grid->column('customer_tag', __('Customer tag'));
        $grid->column('da', __('Da'));
        $grid->column('score_level', __('Score level'));
        $grid->column('home_page', __('Home page'));
        $grid->column('business_status', __('Business status'));
        $grid->column('remarks', __('Remarks'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(BusinessSource::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('email_address', __('Email address'));
        $show->field('other_contact', __('Other contact'));
        $show->field('country_id', __('Country id'));
        $show->field('trade_id', __('Trade id'));
        $show->field('customer_tag', __('Customer tag'));
        $show->field('da', __('Da'));
        $show->field('score_level', __('Score level'));
        $show->field('home_page', __('Home page'));
        $show->field('business_status', __('Business status'));
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
        $form = new Form(new BusinessSource());

        $form->text('email_address', __('Email address'));
        $form->text('other_contact', __('Other contact'));
        $form->number('country_id', __('Country id'));
        $form->number('trade_id', __('Trade id'));
        $form->text('customer_tag', __('Customer tag'));
        $form->number('da', __('Da'));
        $form->switch('score_level', __('Score level'))->default(3);
        $form->text('home_page', __('Home page'));
        $form->switch('business_status', __('Business status'))->default(1);
        $form->text('remarks', __('Remarks'));

        return $form;
    }
}
