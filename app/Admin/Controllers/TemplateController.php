<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Diy\NewDelete;
use App\Models\Template;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\Country;
use App\Models\Trade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;
use App\Admin\Extensions\DiyHandle\TemplateDelete;

class TemplateController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '模板管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Template());
        $grid->model()->where('status', '=', 1);
        $grid->disableFilter();//禁用查询
        $grid->disableExport();//禁用导出
        $grid->column('id', __('模板ID'));
        $grid->column('template_name', __('模板名称'));
        //$grid->column('email_title', __('Email title'));
        //$grid->column('email_content', __('Email content'));
        //$grid->column('template_sign', __('Template sign'));
        $grid->column('country.country_name', __('国家'));
        $grid->column('trade.trade_name', __('行业'));
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
            //  $actions->add(new NewDelete());
            // 老版本添加自定义删除按钮
            $actions->append(new TemplateDelete($actions->getKey()));
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
        $show = new Show(Template::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('template_name', __('Template name'));
        $show->field('email_title', __('Email title'));
        $show->field('email_content', __('Email content'));
        $show->field('template_sign', __('Template sign'));
        $show->field('country_id', __('Country id'));
        $show->field('trade_id', __('Trade id'));
        $show->field('status', __('Status'));
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
        $form = new Form(new Template());
        if(Route::currentRouteName () == 'admin.templates.edit'){
            $form->text('country.country_name', __('国家'))->required()->readonly();
            $form->text('trade.trade_name', __('行业'))->required()->readonly();
            $form->text('template_name', __('模板名称'))->required()->readonly();

        }else{
            $form->select('country_id', __('国家'))->options('/api/countrylist')->required();
            $form->select('trade_id', __('行业'))->options('/api/tradelist')->required();;
            $form->text('template_name', __('模板名称'))->required();
        }

        $form->text('email_title', __('标题'))->required();;
        $form->editor('email_content', __('正文'))->style('height','400px;')->required();;
        //$form->textarea('email_content', __('内容'));
        $form->text('template_sign', __('签名'));

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

        $form->saving(function ($model) {
            if($model->id == null){
                //验证是否已经存在数据
                $where  = [
                    'country_id' => $model->country_id,
                    'trade_id'=>$model->trade_id,
                    //'template_name'=>$model->template_name,
                    'status'=>1,
                ];
                $is_exist = DB::table('templates')->where($where)->count();
                if($is_exist > 0){
                    $error = new MessageBag([
                        'title' => '同一国家和行业只能添加一套模板，如果想更新模板请先删除重复模板内容！'
                    ]);
                    return redirect(url("admin/templates/create"))->with(compact('error'));
                }
            }
        });
        return $form;
    }
}
