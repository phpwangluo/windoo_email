<?php


namespace App\Admin\Extensions\Show;

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
            $now_reply_detail[0]['handle_type'] = $now_reply_detail[0]['reply_status'];
            //根据收发件地址获取邮件列表
            $replyed_list = MailReceived::where([
                'sender_email'=>$reply_sender_email,
                'receiver_email'=>$reply_sreceiver_email,
            ])->where('id','<>',$id)->orderBy('receive_time','desc')->orderBy('id','desc')->get()->toArray();
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
            if($now_reply_detail[0]['reply_status'] == 1){
                $lists_format_end =  array_merge($now_reply_detail,$replyed_list);
            }else{
                $lists_format_end =  $replyed_list;
            }
        }
        $html = '';
        foreach ($lists_format_end as $k => $v) {
            $style = '';
            if($v['handle_type'] == 2){
                $style = 'color:green';
            }elseif ($v['handle_type'] == 1){
                $style = 'color:red';
            }
            $html .= '
            <style>
            table {
                width: 95%;
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
            p{ word-wrap:break-word; width:520px;}
        </style>
        <table>
            <thead>
                <tr>
                    <th colspan="2" style="text-align: center">邮件回复内容</th>
                </tr>
            </thead>
            <tbody>
            <tr style="width: 800px;"><td style="padding: 10px;width: 80px;">联系人</td><td style="padding: 10px;width: 700px;' . $style . '">' . $v['sender_email'] . '</td></tr>
            <tr><td style="padding: 10px;">回复时间</td><td style="padding: 10px;">' . $v['handle_time'] . '</td></tr>
            <tr><td style="padding: 10px;">标题</td><td style="padding: 10px;">' . $v['title'] . '</td></tr>
            <tr><td style="padding: 10px;">正文</td><td style="padding: 10px;">' . $v['content'] . '</td></tr>
            <tr><td style="padding: 10px;">签名</td><td style="padding: 10px;">' . $v['email_sign'] . '</td></tr>
            </tbody>
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
