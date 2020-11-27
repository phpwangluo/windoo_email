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
                    //$messages = $folder->messages()->on('2020-11-27')->all()->get();
                    //$messages = $folder->messages()->unseen()->on('2020-11-25')->all()->get();
                    $messages = $folder->messages()->unseen()->all()->get();
                    $reply_unseen = [];

                    foreach($messages as $k => $message){
                        $content = $message->getStructure()->parts[1]->content;
                        $encoding = $message->getStructure()->parts[1]->encoding;
                        $charset = $message->getStructure()->parts[1]->charset;
                        if($charset != 'utf-8'){
                            $title = iconv($charset,'utf-8',$message->getSubject());
                        }else{
                            $title = $message->getSubject();
                        }
                        $reply_unseen[$k]['sender_email'] = $message->getSender()[0]->mail;
                        $reply_unseen[$k]['receiver_email'] = $message->getTo()[0]->mail;
                        $reply_unseen[$k]['title'] = $title;
                        $reply_unseen[$k]['content'] = $this->ReCoverImapGarbled($content,$encoding,$charset);
                        $reply_unseen[$k]['receive_time'] = date('Y-m-d H:i:s',$message->getDate()->toDate()->getTimestamp());
                        $reply_unseen[$k]['created_at'] = date('Y-m-d H:i:s',time());
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

    /**
     * 解决收取邮件内容乱码报错的问题
     * @param $str
     * @param $encoding
     * @param $charset
     * @return bool|string
     */
    function ReCoverImapGarbled($str,$encoding,$charset){
        if($str == ''){
            return $str;
        }
        if($encoding == 3) {
            if($charset != 'utf-8') {
                $email_content = iconv($charset, 'utf-8', imap_base64($str));
            }else{
                $email_content = imap_base64($str);
            }
        } else if($encoding == 4) {
            if($charset != 'utf-8') {
                $email_content = iconv($charset, 'utf-8', imap_qprint($str));
            }else{
                $email_content = imap_qprint($str);
            }
        } else
        {
            if($charset != 'utf-8') {
                $email_content = iconv($charset, 'utf-8', $str);
            }else{
                $email_content = $str;
            }
        }
        return $email_content;
    }

    /**
     * 大神写的解决乱码问题的参考代码
     * @param $stream
     * @param int $msg_number
     * @param bool $structure
     * @param bool $part_number
     * @return bool|string
     */
    function get_part($stream, $msg_number=0, $structure = false, $part_number = false) //Get Part Of Message Internal Private Use
    {
        $structure = imap_fetchstructure($stream, $msg_number);
        $part_number = "1";
        $text = imap_fetchbody($stream, $msg_number, $part_number);
        if($structure->encoding == 3)
        {
            return iconv('gb2312','utf8',imap_base64($text));
        }
        else if($structure->encoding == 4)
        {
            return iconv('gb2312','utf8',imap_qprint($text));
        }
        else
        {
            return iconv('gb2312','utf8',$text);
        }
        if($structure->type == 1) /* multipart */
        {
            while(list($index, $sub_structure) = each($structure->parts))
            {
                if($part_number)
                {
                    $prefix = $part_number . '.';
                }
                $data = $this->get_part($stream, $msg_number, $sub_structure, $prefix . ($index + 1));
                if($data)
                {
                    return $data;
                }
            }
        }
        return false;
    }
}
