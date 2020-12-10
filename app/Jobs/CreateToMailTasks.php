<?php

namespace App\Jobs;

use App\Models\BusinessSource;
use App\Models\Contact;
use App\Models\Country;
use App\Models\MailForSend;
use App\Models\Sender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        $request_data = [];
        try{
            //获取联系人信息
            $contact_list = Contact::join('templates', 'templates.id', '=', 'contacts.template_id')
                ->where(['contacts.status' => 1])
                ->get()->toArray();
            $insert_forsend = [];
            $cancel_send = [];
            $buiness_source_arr = [];
            $request_data['data']['sender_mail_detail'] = $contact_list;
            $buiness_source = BusinessSource::get();
            if($buiness_source){
                $buiness_source_arr = array_column($buiness_source->toArray(),'email_address');
            }
            foreach ($contact_list as $k => $v){
                //自动发送的邮件，当联系人状态已经变更为停用时，更新已有的还没有对联系人发送的邮件，修改状态为已取消
                if($v['task_status'] == 0){
                    $cancel_send[$k]['receiver_email'] = $v['email_address'];
                    $cancel_send[$k]['send_status'] = 3;
                    //$cancel_send[$k]['updated_at'] = date('Y-m-d H:i:s',time());
                    /*$message = '联系人状态被停用，发送任务被取消';
                    Log::channel('info_create_task')->info($message, $v);*/
                    continue;
                }
                //联系人已经变成合作资源时不创建自动发送任务
                if(in_array($v['email_address'],$buiness_source_arr)){
                    $message = '联系人已经变成合作资源，无法创建发送任务';
                    Log::channel('info_create_task')->info($message, $v);
                    continue;
                }
                //判断是否还有发送次数,发送次数不够的时候不允许创建邮件任务
                if($v['send_max_num'] <= 0){
                    $message = '联系人剩余发送次数用完，无法创建发送任务';
                    Log::channel('info_create_task')->info($message, $v);
                    continue;
                }
                //通过国家ID获取国家对应的时区
                $country_detail = Country::where(['id'=>$v['country_id']])->first();
                date_default_timezone_set($country_detail->timezone);
                //随机获取当地大于当前的一个时间
                $sender_H = date('G',time());
                if($sender_H > $v['send_start_hour'] && $sender_H < $v['send_end_hour']){
                    $sender_time = date('Y-m-d').' '.mt_rand($sender_H,$v['send_end_hour']).':00:00';
                }else if($sender_H > $v['send_end_hour']){
                    //只创建今天要发送的任务,超过发送时间区间，第二天再创建发送任务
                    continue;
                    //$sender_time = date('Y-m-d',strtotime("+1day")).' '.mt_rand($v['send_start_hour'],$v['send_end_hour']).':00:00';
                }else{
                    $sender_time = date('Y-m-d').' '.mt_rand($v['send_start_hour'],$v['send_end_hour']).':00:00';
                }
                //判断要发送邮件对应的日期是否已经发送,如果已经自动发送过则不创建发送任务
                $sender_local_time_date = date('Y-m-d',time());
                $mails_forsend = MailForSend::where([
                    'send_type'=>1,
                    'receiver_email'=>$v['email_address']
                ])->whereBetween('sender_local_time',[$sender_local_time_date.' 00:00:00',$sender_local_time_date.' 23:59:59'])->get('receiver_email')->toArray();
                if(!empty($mails_forsend)){
                    continue;
                }
                if(empty($v['email_title']) || empty($v['template_id']) || empty($v['email_content'])){
                    $message = '联系人没有绑定邮件模板，无法创建发送任务';
                    Log::channel('info_create_task')->info($message, $v);
                    continue;
                }
                //把目标联系人的要发送时间转化成当地服务器的时间
                date_default_timezone_set(\config('app.timezone'));
                $sender_time_server = date('Y-m-d H:i:s',strtotime($sender_time.' '.$country_detail->timezone));
                $insert_forsend[$k]['receiver_email'] = $v['email_address'];
                $insert_forsend[$k]['title'] = $v['email_title'];
                $insert_forsend[$k]['template_id'] = $v['template_id'];
                $insert_forsend[$k]['email_sign'] = $v['template_sign'];
                $insert_forsend[$k]['content'] = $v['email_content'];
                $insert_forsend[$k]['plan_send_time'] = $sender_time_server;
                $insert_forsend[$k]['sender_local_time'] = $sender_time;
                $insert_forsend[$k]['send_type'] = 1;
                $insert_forsend[$k]['send_status'] = 1;
                //$insert_forsend[$k]['created_at'] = date('Y-m-d H:i:s',time());
            }
            //更新邮件状态为已取消
            if(!empty($cancel_send)){
                $receiver_email_arrs = array_column($cancel_send,'receiver_email');
                MailForSend::where([
                    'send_type'=>1
                ])->whereIn('receiver_email', $receiver_email_arrs)->update([
                    'send_status' => 3,
                    //'updated_at'=>date('Y-m-d H:i:s',time())
                ]);
                $message = '联系人状态被停用，自动创建的发送任务全部被取消';
                Log::channel('info_create_task')->info($message, $receiver_email_arrs);
            }
            //写入到mail_for_sends
            if(!empty($insert_forsend)){
                //验证是否还有符合要求的发件箱
                $mail = Sender::join('mail_settings','mail_settings.id','=','senders.mail_setting_id')
                    ->where(['status'=>1,'email_status'=>1])
                    ->whereRaw('send_count<=max_send_count')
                    ->inRandomOrder()
                    ->first();
                if(empty($mail)){
                    $message = '发件箱发送次数用完，联系人发送任务无法创建';
                    Log::channel('info_create_task')->info($message, $insert_forsend);
                }else{
                    MailForSend::insert($insert_forsend);
                    $insert_email_arrs = array_column($insert_forsend,'receiver_email');
                    //写入成功以后需要递减联系人最大使用次数
                    Contact::whereIn('email_address',$insert_email_arrs)->decrement('send_max_num');
                }

            }
            return 1;
        }catch (\Exception $e){
            $message = '创建任务失败:'.$e->getMessage();
            Log::channel('error_gp_email')->error($message, [$request_data['data']]);
        }
    }
}
