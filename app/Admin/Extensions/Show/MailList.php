<?php


namespace App\Admin\Extensions\Show;

use App\Models\MailForSend;
use App\Models\MailReceived;
use Encore\Admin\Show\AbstractField;

class MailList extends AbstractField
{
    public function render($id='')
    {
        $this->escape=false;
        $now_reply_detail = MailReceived::where([
            'id'=>$id
        ])->get()->toArray();
        if(!empty($now_reply_detail)){
            $reply_sender_email = $now_reply_detail[0]['sender_email'];
            $reply_sreceiver_email = $now_reply_detail[0]['receiver_email'];
            $now_reply_detail[0]['template_id'] = 0;
            $now_reply_detail[0]['email_sign'] = '';
            $now_reply_detail[0]['handle_time'] = $now_reply_detail[0]['receive_time'];
            $now_reply_detail[0]['handle_type'] = $now_reply_detail[0]['receive_status'];
            //根据收发件地址获取邮件列表
            $replyed_list = MailReceived::where([
                'sender_email'=>$reply_sender_email,
                'receiver_email'=>$reply_sreceiver_email,
                'receive_status'=>2
            ])->get()->toArray();
            foreach ($replyed_list as $rk=>$rv){
                $replyed_list[$rk]['template_id'] = 0;
                $replyed_list[$rk]['email_sign'] = '';
                $replyed_list[$rk]['handle_time'] = $rv['receive_time'];
                $replyed_list[$rk]['handle_type'] = 2; //客户回复
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
        foreach ($lists_format_end as $k => $v) {
            $style = '';
            if($v['handle_type'] == 2){
                $style = 'style="color:green"';
            }elseif ($v['handle_type'] == 1){
                $style = 'style="color:red"';
            }
            $html .= '
        <table border="1" width="100%">
            <tr><td width="30%">邮箱</td><td width="70%"' . $style . '>' . $v['sender_email'] . '</td></tr>
            <tr><td>发送时间</td><td>' . $v['handle_time'] . '</td></tr>
            <tr><td>主题</td><td>' . $v['title'] . '</td></tr>
            <tr><td>正文</td><td>' . $v['content'] . '</td></tr>
            <tr><td>签名</td><td>' . $v['email_sign'] . '</td></tr>
        </table><hr>';;
        }
        // 返回自定义回复详情页面
        /*$html = '<table border="1" width="100%">
            <tr><td width="30%">邮箱</td><td width="70%" style="color: red">12312@qq.com</td></tr>
            <tr><td>发送时间</td><td>2020-01-12 00:12:23</td></tr>
            <tr><td>主题</td><td>是多少剩的</td></tr>
            <tr><td>正文</td><td>是的撒多阿萨德阿萨德阿萨德阿萨德阿萨德</td></tr>
            <tr><td>签名</td><td>苏州闻道</td></tr>
        </table><hr><table border="1" width="100%">
            <tr><td width="30%">邮箱</td><td width="70%" style="color: red">12312@qq.com</td></tr>
            <tr><td>发送时间</td><td>2020-01-12 00:12:23</td></tr>
            <tr><td>主题</td><td>是多少剩的</td></tr>
            <tr><td>正文</td><td>是的撒多阿萨德阿萨德阿萨德阿萨德阿萨德</td></tr>
            <tr><td>签名</td><td>苏州闻道</td></tr>
        </table><hr>';*/
        return $html;
    }
}
