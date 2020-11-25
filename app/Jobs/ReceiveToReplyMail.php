<?php

namespace App\Jobs;

use App\Models\MailReceived;
use App\Models\Sender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webklex\IMAP\Facades\Client;

class ReceiveToReplyMail implements ShouldQueue
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
        //拉取邮件的业务逻辑
        try{
            //获取有效的可以拉取邮件的邮箱
            $mail = Sender::join('mail_settings','mail_settings.id','=','senders.mail_setting_id')
                ->where(['status'=>1])
                ->whereRaw('send_count<=max_send_count')
                ->inRandomOrder()
                ->get()->toArray();
            foreach ($mail as $k=>$v){
                $config = [
                    'host'          => $v['getmail_host'],
                    'port'          => $v['getmail_port'],
                    'encryption'    => $v['getmail_encryption'],
                    'validate_cert' => true,
                    'username'      => $v['email_address'],
                    'password'      => $v['email_pass'],
                    'protocol'      => $v['getmail_protocol'],
                ];
                $client = Client::make($config);
                //创建连接
                $client->connect();
                //获取收件箱
                $folders = $client->getFolders(false,'Inbox');
                //遍历收件箱中的邮件内容
                foreach($folders as $folder){
                    //获取邮件相关属性
                    /** @var \Webklex\PHPIMAP\Message $message */
                    //$messages = $folder->messages()->on('2020-11-25')->all()->get();
                    $messages = $folder->messages()->unseen()->on('2020-11-25')->all()->get();
                    $reply_unseen = [];
                    foreach($messages as $k => $message){
                        $reply_unseen[$k]['sender_email'] = $message->getSender()[0]->mail;
                        $reply_unseen[$k]['receiver_email'] = $message->getTo()[0]->mail;
                        $reply_unseen[$k]['title'] = iconv($message->getStructure()->parts[0]->charset,'UTF8',$message->getSubject());
                        $reply_unseen[$k]['content'] = $message->getHTMLBody() ? $message->getHTMLBody() : $message->getTextBody();
                        $reply_unseen[$k]['receive_time'] = date('Y-m-d H:i:s',$message->getDate()->toDate()->getTimestamp());
                        $reply_unseen[$k]['created_at'] = date('Y-m-d H:i:s',time());
                        /*if($message->move('INBOX.read') == true){
                            echo 'Message has ben moved';
                        }else{
                            echo 'Message could not be moved';
                        }*/
                    }
                    if(!empty($reply_unseen)){
                        MailReceived::insert($reply_unseen);
                    }
                }

            }
            return ['code' => 1000, 'data' => ['message' => '邮件发送成功!']];
        }catch (\Exception $e){
            return ['code' => 1004, 'data' => ['message' => '邮件发送失败!'.$e->getMessage()]];
        }
    }
}
