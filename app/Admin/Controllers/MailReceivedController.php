<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Diy\ChangeTaskStatusAction;
use App\Models\Country;
use App\Models\MailForSend;
use App\Models\MailReceived;
use App\Models\Template;
use App\Models\Trade;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Admin\Actions\Diy\MailReceivedDetailAction;
use http\Env\Request;
use Ichynul\RowTable\TableRow;

class MailReceivedController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '回复管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MailReceived());
        $grid->model()->where('receive_status', '=', 1);
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('sender_email', '邮箱')->select('/api/contactlist');
            $filter->equal('receive_status', '回复状态')->select([
                1=>'待处理',
                2=>'已处理'
            ]);

        });
        $grid->disableExport();//禁用导出
        $grid->disableCreateButton(); //禁用创建
        //$grid->column('id', __('Id'));
        $grid->column('sender_email', __('邮箱'));
        //$grid->column('receiver_email', __('收邮箱'));
        //$grid->column('title', __('Title'));
        //$grid->column('content', __('Content'));
        $grid->column('contact.country_id', __('国家'))->display(function () {
            $country = Country::where('id',$this->contact->country_id)->first('country_name');
            return $country['country_name'];
        });
        $grid->column('contact.trade_id', __('行业'))->display(function () {
            $trade = Trade::where('id',$this->contact->trade_id)->first('trade_name');
            return $trade['trade_name'];
        });
        $grid->column('contact.customer_tag', __('示例项目'));
        $grid->column('receive_time', __('回复时间'));
        $grid->column('receive_status', __('回复状态'))->using([
            1 => '待处理',
            2 => '使用中',
        ], '未知')->dot([
            1 => 'warning',
            2 => 'success',
        ], 'danger');
        //$grid->column('created_at', __('Created at'));
        //$grid->column('updated_at', __('Updated at'));
        $grid->actions(function ($actions) {

            // 去掉删除
            $actions->disableDelete();

            // 去掉编辑
            //$actions->disableEdit();

            // 去掉查看
            //$actions->disableView();
            // 添加自定义查看的按钮
            $actions->add(new MailReceivedDetailAction());
        });
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        $states = [
            'on' => ['text' => 'YES'],
            'off' => ['text' => 'NO'],
        ];

        $grid->column('switch_group')->switchGroup([
            'hot'       => '热门',
            'new'       => '最新',
            'recommend' => '推荐',
        ], $states);

        $grid->column('是否合作')->switch($states);
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
        $show = new Show(MailReceived::findOrFail($id));
        $show->render('admin/home');
        $show->field('沟通记录')->replylist('xxx');

        $show->divider();
        $show->field('id', __('Id'));
        $show->field('sender_email', __('Sender email'));
        $show->field('receiver_email', __('Receiver email'));
        $show->field('title', __('Title'));
        $show->field('content', __('Content'));
        $show->field('receive_time', __('Receive time'));
        $show->field('receive_status', __('Receive status'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->rowtable('任务信息2', function ($table) {
            $table->row(function (TableRow $row) {
                $row->text('text1', 'label1')->rules('required');
                $row->text('text2', 'label2');
                $row->text('text3', 'label3');
            });
            $table->row(function (TableRow $row) {
                $row->text('text4', 'label4');
                $row->text('text5', 'label5');
                $row->text('text6', 'label6');
            });
            $table->row(function (TableRow $row) {
                $row->text('text7', 'label7');
                $row->text('text8', 'label8');
                $row->text('text9', 'label9');
            });
            //$table->useDiv(false);
            //$table->setHeaders(['h1','h2']);
            //$table->useDiv(false);
            //$table->headersTh(true);//使用table时 头部使用<th></th>，默认使用<td></td>样式有些差别
            //$table->getTableWidget()//extends Encore\Admin\Widgets\Table
            //->offsetSet("style", "width:1000px;");
        });
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MailReceived());

        //$form->text('sender_email', __('Sender email'));
        //$form->text('receiver_email', __('Receiver email'));
        //$form->text('title', __('Title'));
        //$form->textarea('content', __('Content'));
        //$form->datetime('receive_time', __('Receive time'))->default(date('Y-m-d H:i:s'));
        //$form->switch('receive_status', __('Receive status'))->default(1);
        //获取我发已发送的邮件记录
        $sended_list = MailForSend::where(['send_status'=>2,])->get()->toArray();
        $reply_list  = MailReceived::get()->toArray();
        $html = '';
        foreach ($reply_list as $k => $v){
            $html .= '
        --------------------------------------------------------------------------------------------------------------
        ---------------------------------------------------------------
        <table border="1" width="100%">
            <tr><td width="30%">邮箱</td><td width="70%">'.$v['sender_email'].'</td></tr>
            <tr><td>发送时间</td><td>'.$v['receive_time'].'</td></tr>
            <tr><td>主题</td><td>'.$v['title'].'</td></tr>
            <tr><td>正文</td><td>'.$v['content'].'</td></tr>
            <tr><td>签名</td><td>苏州闻道</td></tr>
        </table>';
        }
        $form->html($html,  __('邮件记录：'));
        return $form;
    }
}
