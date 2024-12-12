<?php

namespace App\Admin\Controllers;

use App\Models\Trade;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use App\Admin\Extensions\DiyHandle\TradeDelete;

class TradeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '行业管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Trade());
        $grid->model()->where('status', '=', 1);
        $grid->disableFilter();//禁用查询
        $grid->disableExport();//禁用导出
        //$grid->column('id', __('Id'));
        $grid->column('trade_name', __('行业'));
        //$grid->column('created_at', __('Created at'));
        //$grid->column('updated_at', __('Updated at'));
        $grid->actions(function ($actions) {

            // 去掉删除
            $actions->disableDelete();

            // 去掉编辑
            $actions->disableEdit();

            // 去掉查看
            $actions->disableView();

            // 添加自定义删除按钮
            //$actions->add(new NewDelete());

            // 添加操作
            if($actions->row->trade_name != '全部'){
                $actions->append(new TradeDelete($actions->getKey()));
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
        $show = new Show(Trade::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('trade_name', __('Trade name'));
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
        $form = new Form(new Trade());
        $form->tags('trade_name', __('行业'))
            ->required()
            ->placeholder('使用英文逗号分割，可以一次添加多个！');
        //$form->text('trade_name', __('行业'));
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
            $trade_name_arr = $model->trade_name;
            //验证是否已经存在数据
            $is_exist = DB::table('trades')->where(['status'=>1])->whereIn('trade_name',$trade_name_arr)->count();
            if($is_exist > 0){
                $error = new MessageBag([
                    'title' => '添加的行业中有重复的行业，请保证行业唯一后再添加！'
                ]);
                return redirect(url("admin/trades/create"))->with(compact('error'));
            }
            // 从$model取出数据并进行处理
            $inser_arr = [];

            DB::table('trades')->insert($inser_arr);
            foreach ($trade_name_arr as $post) {
                if($post != ''){
                    $inser_arr[]['trade_name'] = $post;
                }
            }
            DB::table('trades')->insert($inser_arr);
            return redirect('/admin/trades');

        });
        return $form;
    }
}
