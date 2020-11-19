<?php

namespace App\Http\Controllers\Emails;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\MailSended;
use Illuminate\Http\Request;

use App\Mail\ContactSender;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Sender;
use App\Models\MailForSend;
use League\Flysystem\Config;
use \Exception;

class SendmailController extends Controller
{
    public function test(Request $request)
    {
        try{
            //获取邮件的发送信息
            $mail_for_send = MailForSend::where('send_status',1)
                ->orWhere('send_status',4)->get();
            foreach ($mail_for_send as $k => $v){
                //获取有效的发件人多个的话随机选择发件人
                $mail = Sender::join('mail_settings','mail_settings.id','=','senders.mail_setting_id')
                    ->where(['status'=>1])
                    ->whereRaw('send_count<=max_send_count')
                    ->first();
                //$mail=DB::table('mail_settings')->first();
                $re = explode('@', $mail->email_address);
                $config = array(
                    'driver' => $mail->driver,
                    'host' => $mail->host,
                    'port' => $mail->port,
                    'from' => array('address' => $mail->email_address, 'name' => $re[0]),
                    'encryption' => $mail->encryption,
                    'username' => $mail->email_address,
                    'password' => $mail->email_pass,
                    //'sendmail' => '/usr/sbin/sendmail -bs',
                    'pretend' => false
                );
                Config()->set('mail',$config);
                $subject = $v['title'];
                $viewData = [
                    'content' => $v['content'],
                ];
                Mail::to($v['receiver_email'])->send(new ContactSender($subject, $viewData));
                //更新发件人发件次数
                DB::table('senders')->increment('send_count');
                //更新发件箱邮件状态
                MailForSend::where('receiver_email',$v['receiver_email'])
                    ->update(['send_status' => 2]);
                //向收件箱中写入一条发件记录
                $sended = [];
                $sended['sender_email'] = $mail->email_address;
                $sended['receiver_email'] = $v['receiver_email'];
                $sended['title'] = $v['title'];
                $sended['content'] = $v['content'];
                $sended['send_time'] = $sended['created_at'] = date('Y-m-d H:i:s',time());
                $sended['send_status'] = 2;
                MailSended::insert($sended);
                //更新某个联系人的收到邮箱的次数
                DB::table('contacts')->where(['email_address'=>$v['receiver_email']])->increment('send_count');
            }
            return ['code' => 1000, 'data' => ['message' => '邮件发送成功!']];
        }catch (\Exception $e){
            return ['code' => 1004, 'data' => ['message' => '邮件发送失败!']];
        }

    }
}
