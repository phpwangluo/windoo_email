<?php

namespace App\Admin\Controllers;

use App\Models\Country;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;

class CountryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '国家管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Country());
        //$grid->disableCreateButton(); //禁用添加
        $grid->disableFilter();//禁用查询
        $grid->disableExport();//禁用导出
        //$grid->column('id', __('Id'));
        $grid->column('country_name', __('国家'));
        $grid->column('send_start_hour', __('开始时间'));
        $grid->column('send_end_hour', __('结束时间'));
        //$grid->column('created_at', __('Created at'));
        //$grid->column('updated_at', __('Updated at'));

        $grid->actions(function ($actions) {

            // 去掉删除
            $actions->disableDelete();

            // 去掉编辑
            $actions->disableEdit();

            // 去掉查看
            $actions->disableView();
            if ($actions->row->country_name != '全部') {
                //$actions->disableEdit();
                $actions->prepend('<a
                title="编辑"
                href="'.$this->getResource().'/'.$this->getRouteKey().'/edit"
                class="'.$this->grid->getGridRowName().'-edit">
                <i class="fa fa-edit"></i>');
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
        $show = new Show(Country::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('country_name', __('Country name'));
        $show->field('send_start_hour', __('Start'));
        $show->field('send_end_hour', __('End'));
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
        $form = new Form(new Country());
        $form->hidden('id', __('id'));
        if(Route::currentRouteName () == 'admin.countries.edit'){
            $form->text('country_name', __('国家'))->required()->readonly();
            $form->text('country_code', __('国家编码'))->required()->readonly();
            $form->text('timezone', __('时区'))->required()->readonly();

        }else{
            $form->text('country_name', __('国家'))->required();
            $form->text('country_code', __('国家编码'))->required();
            $form->text('timezone', __('时区'))->required();
        }
        $form->number('send_start_hour', __('开始时间'))->required()->default(9)->min(0)->max(23);
        $form->number('send_end_hour', __('结束时间'))->required()->default(17)->min(0)->max(23);
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
                $country_name = $model->country_name;
                //验证是否已经存在数据
                $is_exist = DB::table('countries')->where(['status'=>1])->where(['country_name'=>$country_name])->count();
                if($is_exist > 0){
                    $error = new MessageBag([
                        'title' => '添加的国家中有重复的国家，请保证国家唯一后再添加！'
                    ]);
                    return redirect(url("admin/countries/create"))->with(compact('error'));
                }
            }
        });

        return $form;
    }
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // 从$model取出数据并进行处理

        });
    }
}
