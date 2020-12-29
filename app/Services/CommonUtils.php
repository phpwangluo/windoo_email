<?php


namespace App\Services;


use App\Models\MailForSend;
use App\Models\Sender;

class CommonUtils
{
    function chooseSenders($email_address){
        //验证是否已经给联系人发送过邮件
        $is_has_sender = MailForSend::query()->where('receiver_email','=',$email_address)->first();
        if(!$is_has_sender){
            $mail = Sender::query()->leftJoin('mail_settings','mail_settings.id','=','senders.mail_setting_id')
                ->where(['senders.status'=>1,'senders.email_status'=>1])
                ->whereRaw('senders.send_count<=senders.max_send_count')
                ->inRandomOrder()
                ->first();
            if(!$mail){
                return false;
            }
            return $mail;
        }
        //存在判断发件箱对应的发送次数
        $mail = Sender::query()->leftJoin('mail_settings','mail_settings.id','=','senders.mail_setting_id')
            ->where([
                'senders.status'=>1,
                'senders.email_status'=>1,
                'senders.email_address'=>$is_has_sender->sender_email
                ])
            ->whereRaw('senders.send_count<=senders.max_send_count')
            ->first();
        if(!$mail){
            return false;
        }
        return $mail;
    }
}
