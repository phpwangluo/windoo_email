<?php

namespace App\Http\Controllers\Emails;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Country;
use App\Models\MailSended;
use Illuminate\Http\Request;

use App\Mail\ContactSender;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Sender;
use App\Models\MailForSend;
use League\Flysystem\Config;
use \Exception;
use Illuminate\Support\Facades\Response;

class SendmailController extends Controller
{
    public function test(Request $request)
    {
        try{
            //获取邮件的发送信息
            $mail_for_send = MailForSend::select(['mail_for_sends.*', 'contacts.*','mail_for_sends.id as sendid'])
                ->join('contacts','contacts.email_address','=','mail_for_sends.receiver_email')->where('send_status',1)
                ->get(); //获取当天可以发送邮件的联系人
            foreach ($mail_for_send as $k => $v){
                //通过国家ID获取国家对应的时区
                $country_detail = Country::where(['id'=>$v['country_id']])->first();
                date_default_timezone_set($country_detail->timezone);
                //判断联系人状态是否为：启用，否则终止发送邮件
                if($v['task_status'] != 1){
                    continue;
                }
                //验证收件人的发送次数是否用完，用完不发送，除非修改模板内容
                if($v['send_max_num'] <= 0){
                    continue;
                }
                //区分自动发送与手动发送的区别
                if($v['send_type'] == 1){
                    //自动发送
                    //今天是否已经发送邮件，自动邮件默认一天只能发一次
                    //今天已发送记录里是否有邮件记录
                    $today_sended_email = MailForSend::where([
                        'send_status'=>2,
                        'receiver_email'=>$v['receiver_email'],
                        'send_type'=>1,
                    ])->whereBetween('real_send_time',[date('Y-m-d',time()).' 00:00:00',date('Y-m-d',time()).' 23:59:59'])
                        ->get()->toArray();
                    if(!empty($today_sended_email)){
                        continue;
                    }
                    //当前时间是否在允许发送邮件的范围内
                    $hour  = date('H',time());
                    if($hour != date('H',strtotime($v['plan_send_time']))){
                        continue;
                    }
                }
                //获取有效的发件人，多个的话随机选择发件人
                $mail = Sender::join('mail_settings','mail_settings.id','=','senders.mail_setting_id')
                    ->where(['status'=>1])
                    ->whereRaw('send_count<=max_send_count')
                    ->inRandomOrder()
                    ->first();
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

                //发送邮件
                Mail::to($v['receiver_email'])->send(new ContactSender($subject, $viewData));
                //更新发件人发件次数
                DB::table('senders')->where(['email_address'=>$mail->email_address])->increment('send_count');
                //更新发件箱邮件状态
                MailForSend::where('id',$v['sendid'])
                    ->update([
                        'send_status' => 2,
                        'sender_email'=>$mail->email_address,
                        'real_send_time'=>date('Y-m-d H:i:s',time()),
                    ]);
                //更新某个联系人的收到邮箱的次数
                DB::table('contacts')->where(['email_address'=>$v['receiver_email']])->increment('send_count');
                date_default_timezone_set('Asia/Shanghai');
            }
            return ['code' => 1000, 'data' => ['message' => '邮件发送成功!']];
        }catch (\Exception $e){
            return ['code' => 1004, 'data' => ['message' => '邮件发送失败!'.$e->getMessage()]];
        }
    }
}
