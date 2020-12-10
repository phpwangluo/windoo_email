<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DiyHandle\ChangeResourceStatus;
use App\Models\Country;
use App\Models\MailForSend;
use App\Models\MailReceived;
use App\Models\Trade;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;

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
        $grid->model()
            ->fromSub('select * from mail_receiveds order by sender_email,receive_time DESC limit 11111','a')
            ->groupBy(['a.sender_email','a.receiver_email'])
            ->orderBy('receive_status','asc');
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('sender_email', '联系人')->email();
            $filter->equal('receive_status', '处理状态')->select([
                1=>'待处理',
                2=>'已处理'
            ]);
            $filter->equal('reply_status', '回复状态')->select([
                1=>'待回复',
                2=>'已回复'
            ]);

        });
        $grid->disableExport();//禁用导出
        $grid->disableCreateButton(); //禁用创建
        $grid->column('sender_email', __('联系人'));
        $grid->column('contact.country_id', __('国家'))->display(function () {
            $country = Country::where('id',$this->contact->country_id)->first('country_name');
            return $country['country_name'];
        });
        $grid->column('contact.trade_id', __('行业'))->display(function () {
            $trade = Trade::where('id',$this->contact->trade_id)->first('trade_name');
            return $trade['trade_name'];
        });
        $grid->column('contact.customer_tag', __('项目'));
        $grid->column('receive_time', __('回复时间'));
        $grid->column('reply_status', __('回复状态'))->display(function () {
            if ($this->receive_status == 2) {
                $this->reply_status = 0;
            }
            return $this->reply_status;
        })->using([
            0 => '任务结束',
            1 => '待回复',
            2 => '已回复',
        ], '未知')->label([
            0 => 'info',
            1 => 'warning',
            2 => 'success',
        ], 'info')->sortable();
        $grid->column('receive_status', __('处理状态'))->using([
            1 => '待处理',
            2 => '已处理',
        ], '未知')->dot([
            1 => 'warning',
            2 => 'success',
        ], 'danger')->sortable();
        /*$grid->column('resource_status', __('合作意向'))
            ->editable('select', [0 => '不合作', 2 => '合作中', 3 => '已合作']);*/
        $grid->actions(function ($actions) {

            // 去掉删除
            $actions->disableDelete();

            // 去掉编辑
            $actions->disableEdit();

            // 去掉查看
            $actions->disableView();
            // 添加自定义查看的按钮
            /*if($actions->row->receive_status == 1){
                $actions->add(new MailReceivedDetailAction());
                $actions->add(new ChangeBusinessStatusAction());
                $actions->add(new DoReplyByUserAction());
            }*/
            // prepend一个操作
            if($actions->row->receive_status == 1){
                if($actions->row->reply_status == 1){
                    $actions->prepend('<a title="回复" href="'.$this->getResource().'/'.$actions->getKey().'/edit"><i class="fa fa-mail-reply"></i></a>&nbsp;&nbsp;');
                }
                $actions->prepend('<a title="回复记录" href="'.$this->getResource().'/'.$actions->getKey().'"><i class="fa fa-list-alt"></i></a>&nbsp;&nbsp;');
                $actions->append(new ChangeResourceStatus($actions->getKey()));
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
        $show = new Show(MailReceived::findOrFail($id));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                //$tools->disableList();
                $tools->disableDelete();
            });;
        $show->content('回复记录')->unescape()->replylist($id);
        //$show->field('id', __('Id'));
        //$show->field('sender_email', __('Sender email'));
        //$show->field('receiver_email', __('Receiver email'));
        //$show->field('title', __('Title'));
        //$show->field('content', __('Content'));
        //$show->field('receive_time', __('Receive time'));
        //$show->field('receive_status', __('Receive status'));
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
        $form = new Form(new MailReceived());
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
            $footer->disableReset();

            // 去掉`提交`按钮
            //$footer->disableSubmit();
            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();

            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();

        });
        //$form->text('sender_email', __('Sender email'));
        //$form->text('receiver_email', __('Receiver email'));
        //$form->text('title', __('Title'));
        //$form->textarea('content', __('Content'));
        //$form->datetime('receive_time', __('Receive time'))->default(date('Y-m-d H:i:s'));
        //$form->switch('receive_status', __('Receive status'))->default(1);
        //获取我发已发送的邮件记录
        if($form->isEditing()){
            //获取ID
            $id=request()->route()->parameters()['mail_received'];
            //通过ID获取发件人和收件人等信息
            $now_reply_detail = MailReceived::where([
                'id'=>$id
            ])->get()->toArray();
            if(!empty($now_reply_detail)){
                $reply_sender_email = $now_reply_detail[0]['sender_email'];
                $reply_sreceiver_email = $now_reply_detail[0]['receiver_email'];
                $now_reply_detail[0]['template_id'] = 0;
                $now_reply_detail[0]['email_sign'] = '';
                $now_reply_detail[0]['handle_time'] = $now_reply_detail[0]['receive_time'];
                $now_reply_detail[0]['handle_type'] = $now_reply_detail[0]['reply_status'];
                //根据收发件地址获取邮件列表
                $replyed_list = MailReceived::where([
                    'sender_email'=>$reply_sender_email,
                    'receiver_email'=>$reply_sreceiver_email,
                    'reply_status'=>2
                ])->orderBy('receive_time','desc')->get()->toArray();
                foreach ($replyed_list as $rk=>$rv){
                    $replyed_list[$rk]['template_id'] = 0;
                    $replyed_list[$rk]['email_sign'] = '';
                    $replyed_list[$rk]['handle_time'] = $rv['receive_time'];
                    $replyed_list[$rk]['handle_type'] = $rv['reply_status']; //客户回复
                }
                /*$sended_list = MailForSend::where([
                    'sender_email'=>$reply_sreceiver_email,
                    'receiver_email'=>$reply_sender_email,
                    'send_status'=>2
                ])->get()->toArray();
                foreach ($sended_list as $sk=>$sv){
                    $sended_list[$sk]['handle_time'] = $sv['real_send_time'];
                    $sended_list[$sk]['handle_type'] = 1; //回复客户
                }
                $lists = array_merge($replyed_list,$sended_list);
                $lists_format = collect($lists)
                    ->sortBy(function($item) {
                        return $item['template_id'] . '.' . $item['handle_time'];
                    })
                    ->all();
                $lists_format_end = array_merge($now_reply_detail,$lists_format);*/
                $lists_format_end =  array_merge($now_reply_detail,$replyed_list);
            }
            $html = '';
            foreach ($lists_format_end as $k => $v){
                $style = '';
                if($v['handle_type'] == 2){
                    $style = 'color:green';
                }elseif ($v['handle_type'] == 1){
                    $style = 'color:red';
                }
                $html .= '
        <style>
            table {
                width: 100%;
                border-spacing: 0;/**设置相邻单元格的边框间的距离**/
                border-collapse: collapse;/**边框会合并为一个单一的边框**/
                color:#5a5a5a;
                /**table-layout: fixed;/**固定table表格**/
            }
            table  thead {
                background-color: #d9edf7;
            }
            table td,table th{
                border:1px solid #ccc;
                /**overflow: hidden;/**溢出隐藏**/
                /**white-space: nowrap;/**不换行**/
                text-overflow: ellipsis;/**溢出不可见部分使用...代替**/
            }
        </style>
        <table>
            <thead>
                <tr>
                    <th colspan="2" style="text-align: center">邮件回复内容</th>
                </tr>
            </thead>
            <tbody>
            <tr><td style="padding: 10px;">联系人</td><td style="padding: 10px;' . $style . '">' . $v['sender_email'] . '</td></tr>
            <tr><td style="padding: 10px;">回复时间</td><td style="padding: 10px;">' . $v['handle_time'] . '</td></tr>
            <tr><td style="padding: 10px;">标题</td><td style="padding: 10px;">' . $v['title'] . '</td></tr>
            <tr><td style="padding: 10px;">正文</td><td style="padding: 10px;">' . $v['content'] . '</td></tr>
            <tr><td style="padding: 10px;">签名</td><td style="padding: 10px;">' . $v['email_sign'] . '</td></tr>
            </tbody>
        </table><hr>';
                ;
            }
            $form->html($html,  __('回复记录：'));
            /*$form->select('business_status', __('联系人合作意向'))->options([
            '0'=>'不合作',
            '1'=>'合作中',
            '2'=>'已合作'
        ]);*/
            $sended_list_thenewest = MailForSend::where([
                'sender_email'=>$reply_sreceiver_email,
                'receiver_email'=>$reply_sender_email,
            ])->orderBy('real_send_time','desc')->first();
            $content = '';
            if($sended_list_thenewest){
                $content = $sended_list_thenewest->content;
            }
            $form->hidden('id');
            $form->text('email_for_send','联系人')->default($now_reply_detail[0]['sender_email'])->readonly();
            $form->text('title_for_send','标题');
            $form->editor('content_for_send', '正文')->default($content)->style('height','400px;');
            $form->text('email_sign_for_send', '签名');
            $form->saving(function ($model) {
                //回去回复邮件内容，并写入发送邮件任务列表
                $insert_forsend = [];
                $insert_forsend['receiver_email'] = $model->email_for_send;
                $insert_forsend['title'] = $model->title_for_send;
                $insert_forsend['template_id'] = 0;
                $insert_forsend['email_sign'] = $model->email_sign_for_send;
                $insert_forsend['content'] = $model->content_for_send;;
                $insert_forsend['plan_send_time'] = date('Y-m-d H:i:s',time());
                $insert_forsend['send_type'] = 2;
                $insert_forsend['send_status'] = 1;
                //$insert_forsend['created_at'] = date('Y-m-d H:i:s',time());
                MailForSend::insert($insert_forsend);
                //把邮件状态变成已处理
                MailReceived::where('id',$model->id)
                    ->update([
                        'reply_status'=>2,
                    ]);
                $success = new MessageBag([
                    'title'   => '提示',
                    'message' => '回复成功',
                ]);
                return redirect(url("admin/mail-receiveds"))->with(compact('success'));
            });

        }
        return $form;
    }
}
