<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Diy\ExportTemplateSenderAction;
use App\Admin\Actions\Diy\NewDelete;
use App\Admin\Actions\Diy\ImportSenderAction;
use App\Exports\Export;
use App\Models\Sender;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Admin\Extensions\Tools\ExcelExpoter;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Admin\Extensions\DiyHandle\SenderDelete;

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
        $grid->disableExport('false');//禁用导出
        $grid->disableCreateButton(); //禁用创建
        //$grid->column('id', __('Id'));
        $grid->column('mailsetting.support_name', __('运营商'));
        $grid->column('email_address', __('发件人'));
        //$grid->column('email_pass', __('Email pass'));
        //$grid->column('email_sign', __('Email sign'));
        $grid->column('send_count', __('发送邮件数'))->sortable()->totalRow();
        $grid->column('receive_count', __('接收回复数'));
        //$grid->column('max_send_count', __('Max send count'));
        $grid->column('email_status', __('邮箱状态'))->using([
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
            //$actions->add(new NewDelete());
            // 老版本添加自定义删除按钮
            if($actions->row->email_status == 0){
                $actions->append(new SenderDelete($actions->getKey()));
            }
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
        //导出模板
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new ExportTemplateSenderAction());
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
       /* $form->select('mail_setting_id', __('运营商'))->options('/api/samemailsetiinglists',
            ['prefix' => 'outlook'])->required();*/
        #$form->text('mailsetting.support_name', __('运营商'))->required()->readonly();
        $form->text('email_address', __('发件人'))->required()->readonly();
        $form->text('email_pass', __('密码'))->required();
        //$form->text('email_sign', __('Email sign'));
        //$form->number('send_count', __('Send count'));
        //$form->number('receive_count', __('Receive count'));
        //$form->number('max_send_count', __('Max send count'))->default(1000);
        //$form->switch('email_status', __('状态'))->default(1);

        $states = [
            'on'  => ['value' => 1, 'text' => '启用', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
        ];
        $form->switch('email_status', __('邮箱状态'))->states($states)->default(1);
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

    public function export()
    {
        //设置表头
        $row = [[
            "id"=>'ID',
        ]];
        dd('sdfs');
        //数据
        $list=[];
        //导出
        $data = $list;//要导入的数据
        $header = $row;//导出表头
        $excel = new Export($data, $header,'sdsd');
        $excel->setColumnWidth(['B' => 40, 'C' => 40]);
        $excel->setRowHeight([1 => 40, 2 => 50]);
        $excel->setFont(['A1:Z1265' => '宋体']);
        $excel->setFontSize(['A1:I1' => 14,'A2:Z1265' => 10]);
        $excel->setBold(['A1:Z2' => true]);
        $excel->setBackground(['A1:A1' => '808080','C1:C1' => '708080']);
        $excel->setMergeCells(['A1:I1']);
        $excel->setBorders(['A2:D5' => '#000000']);
        return Excel::download($excel, 'asdasd.xlsx');
    }
}
