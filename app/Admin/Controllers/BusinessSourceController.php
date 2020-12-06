<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Diy\ChangeTaskStatusAction;
use App\Models\BusinessSource;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Route;

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
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->equal('country_id', '国家')->select('/api/countrylist');
            $filter->equal('trade_id', '行业')->select('/api/tradelist');
            $filter->equal('email_address', '联系人')->email();
            $filter->equal('score_level', '评级')->select([
                '1'=>'低',
                '2'=>'中',
                '3'=>'高'
            ]);
        });
        $grid->disableExport();//禁用导出
        $grid->disableCreateButton(); //禁用创建
        //$grid->column('id', __('Id'));
        $grid->column('email_address', __('联系人'));
        $grid->column('country.country_name', __('国家'));
        $grid->column('trade.trade_name', __('行业'));
        $grid->column('other_contact', __('其他联系方式'));
        //$grid->column('customer_tag', __('Customer tag'));
        //$grid->column('da', __('Da'));
        $grid->column('score_level', __('评级'))->using([
            1 => '低',
            2 => '中',
            3 => '高',
        ], '未知');
        //$grid->column('home_page', __('Home page'));
        $grid->column('business_status', __('合作状态'))->using([
            0 => '不合作',
            1 => '合作中',
            2 => '已合作',
        ], '未知')->dot([
            0 => 'danger',
            1 => 'warning',
            2 => 'success',
        ], 'warning');
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
        $show = new Show(BusinessSource::findOrFail($id));

        //$show->field('id', __('Id'));
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
        $form->tools(function (Form\Tools $tools) {

            // 去掉`列表`按钮
            //$tools->disableList();

            // 去掉`删除`按钮
            $tools->disableDelete();
            // 去掉`查看`按钮
            $tools->disableView();

        });
        if(Route::currentRouteName () == 'admin.business-sources.edit'){
            $form->text('email_address', __('联系人'))->readonly();
            $form->text('country.country_name', __('国家'))->readonly();
            $form->text('trade.trade_name', __('行业'))->readonly();
        }else{
            $form->text('email_address', __('联系人'));
            $form->select('country_id', __('国家'))->options('/api/countrylist');
            $form->select('trade_id', __('行业'))->options('/api/tradelist');
        }
        $form->text('customer_tag', __('项目'))->readonly();

        $form->text('other_contact', __('其他联系方式'))->required();
        $form->number('da', __('DA值'));
        $form->select('score_level', __('评级'))->options([
            '1'=>'低',
            '2'=>'中',
            '3'=>'高'
        ]);

        $form->text('home_page', __('网址'))->required();
        $form->select('business_status', __('合作状态'))->options([
            '0'=>'不合作',
            '1'=>'合作中',
            '2'=>'已合作'
        ])->default(1);
        $form->text('remarks', __('备注描述'));
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
