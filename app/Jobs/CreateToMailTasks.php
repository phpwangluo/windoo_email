<?php

namespace App\Jobs;

use App\Mail\ContactSender;
use App\Models\Contact;
use App\Models\Country;
use App\Models\MailForSend;
use App\Models\Sender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CreateToMailTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            //获取联系人信息
            //验证是否已经存在数据
            $contact_list = Contact::join('templates', 'templates.id', '=', 'contacts.template_id')
                ->where(['contacts.status' => 1])
                ->get()->toArray();
            //发送邮件任务列表
            /*$mails_forsend = MailForSend::where([
                'send_type'=>1,
            ])->whereBetween('created_at',[date('Y-m-d',time()).' 00:00:00',date('Y-m-d',time()).' 23:59:59'])->get('receiver_email')->toArray();
            $mails_forsend_formate = array_column($mails_forsend,'receiver_email');*/
            $insert_forsend = [];
            $cancel_send = [];
            foreach ($contact_list as $k => $v){
                //当联系人状态已经变更为停用时，更新已有的还没有对联系人发送的邮件，修改状态为已取消
                if($v['task_status'] == 0){
                    $cancel_send[$k]['receiver_email'] = $v['email_address'];
                    $cancel_send[$k]['send_status'] = 3;
                    $cancel_send[$k]['updated_at'] = date('Y-m-d H:i:s',time());
                    continue;
                }
                //判断是否还有发送次数,发送次数不够的时候不允许创建邮件任务
                if($v['send_max_num'] <= 0){
                    continue;
                }
                //判断要发送邮件对应的日期是否已经发送,如果已经自动发送过则不创建发送任务
                /*if(!empty($mails_forsend_formate)
                    && in_array($v['email_address'],$mails_forsend_formate)){
                    continue;
                }*/
                //通过国家ID获取国家对应的时区
                $country_detail = Country::where(['id'=>$v['country_id']])->first();
                date_default_timezone_set($country_detail->timezone);
                //随机获取当地大于当前的一个时间
                $sender_H = date('G',time());
                if($sender_H > $v['send_start_hour'] && $sender_H < $v['send_end_hour']){
                    $sender_time = date('Y-m-d').' '.mt_rand($sender_H,$v['send_end_hour']).':00:00';
                }else if($sender_H > $v['send_end_hour']){
                    $sender_time = date('Y-m-d',strtotime("+1 day")).' '.mt_rand($v['send_start_hour'],$v['send_end_hour']).':00:00';
                }else{
                    $sender_time = date('Y-m-d').' '.mt_rand($v['send_start_hour'],$v['send_end_hour']).':00:00';
                }
                //把目标联系人的要发送时间转化成当地服务器的时间
                date_default_timezone_set(\config('app.timezone'));
                $sender_time_server = date('Y-m-d H:i:s',strtotime($sender_time.' '.$country_detail->timezone));
                $sender_time_server_date = date('Y-m-d',strtotime($sender_time.' '.$country_detail->timezone));
                //判断要发送邮件对应的日期是否已经发送,如果已经自动发送过则不创建发送任务
                $mails_forsend = MailForSend::where([
                    'send_type'=>1,
                    'receiver_email'=>$v['email_address']
                ])->whereBetween('plan_send_time',[$sender_time_server_date.' 00:00:00',$sender_time_server_date.' 23:59:59'])->get('receiver_email')->toArray();
                if(!empty($mails_forsend)){
                    continue;
                }
                $insert_forsend[$k]['receiver_email'] = $v['email_address'];
                $insert_forsend[$k]['title'] = $v['email_title'];
                $insert_forsend[$k]['template_id'] = $v['template_id'];
                $insert_forsend[$k]['email_sign'] = $v['template_sign'];
                $insert_forsend[$k]['content'] = $v['email_content'];
                $insert_forsend[$k]['plan_send_time'] = $sender_time_server;
                $insert_forsend[$k]['sender_local_time'] = $sender_time;
                $insert_forsend[$k]['send_type'] = 1;
                $insert_forsend[$k]['send_status'] = 1;
                $insert_forsend[$k]['created_at'] = date('Y-m-d H:i:s',time());
            }
            //更新邮件状态为已取消
            if(!empty($cancel_send)){
                $receiver_email_arrs = array_column($cancel_send,'receiver_email');
                MailForSend::whereIn('receiver_email', $receiver_email_arrs)->update(['send_status' => 3,'updated_at'=>date('Y-m-d H:i:s',time())]);
            }
            //写入到mail_for_sends
            if(!empty($insert_forsend)){
                MailForSend::insert($insert_forsend);
                $insert_email_arrs = array_column($insert_forsend,'receiver_email');
                //写入成功以后需要递减联系人最大使用次数
                Contact::whereIn('email_address',$insert_email_arrs)->decrement('send_max_num');
            }
            return ['code' => 1000, 'data' => ['message' => '任务写入成功!']];
        }catch (\Exception $e){
            return ['code' => 1004, 'data' => ['message' => '任务写入失败!'.$e->getMessage()]];
        }
    }
}
