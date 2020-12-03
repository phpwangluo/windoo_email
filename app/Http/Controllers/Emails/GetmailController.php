<?php

namespace App\Http\Controllers\Emails;

use App\Http\Controllers\Controller;
use App\Mail\ContactSender;
use App\Models\MailForSend;
use App\Models\MailReceived;
use App\Models\Sender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PhpParser\Builder\Class_;
use Webklex\IMAP\Facades\Client;

class GetmailController extends Controller
{
    //获取某个邮箱的邮件内容

    public function getEmail(Request $request){
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
                /** @var \Webklex\PHPIMAP\Client $client */
                $client = Client::make($config);
                //创建连接
                /* Alternative by using the Facade
                $client = Webklex\IMAP\Facades\Client::account('default');
                */
                //Connect to the IMAP Server
                $client->connect();
                //获取收件箱
                //Get all Mailboxes
                /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
                $folders = $client->getFolders(false,'Inbox');
                //遍历收件箱中的邮件内容
                //Loop through every Mailbox
                /** @var \Webklex\PHPIMAP\Folder $folder */
                foreach($folders as $folder){
                    //获取邮件相关属性
                    //Get all Messages of the current Mailbox $folder
                    /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
                    //$messages = $folder->messages()->on('2020-12-01')->all()->get();
                    //$messages = $folder->messages()->unseen()->on('2020-11-25')->all()->get();
                    $messages = $folder->messages()->unseen()->all()->get();
                    /** @var \Webklex\PHPIMAP\Message $message */
                    $reply_unseen = [];
                    foreach($messages as $kk => $message){
                        $content = $message->getStructure()->parts[1]->content;
                        $encoding = $message->getStructure()->parts[1]->encoding;
                        $charset = $message->getStructure()->parts[1]->charset;
                        if($charset != 'utf-8'){
                            $title = iconv($charset,'utf-8',$message->getSubject());
                        }else{
                            $title = $message->getSubject();
                        }
                        $email_content = $this->ReCoverImapGarbled($content,$encoding,$charset);

                        $reply_unseen[$kk]['sender_email'] = $message->getSender()[0]->mail;
                        $reply_unseen[$kk]['receiver_email'] = $message->getTo()[0]->mail;
                        $reply_unseen[$kk]['title'] = $title;
                        $reply_unseen[$kk]['content'] = $email_content != '' ? $email_content : ($message->getHTMLBody() ? $message->getHTMLBody() : $message->getTextBody()) ;
                        $reply_unseen[$kk]['receive_time'] = date('Y-m-d H:i:s',$message->getDate()->toDate()->getTimestamp());
                        $reply_unseen[$kk]['created_at'] = date('Y-m-d H:i:s',time());
                    }
                    if(!empty($reply_unseen)){
                        MailReceived::insert($reply_unseen);
                    }
                }
            }
            return ['code' => 1000, 'data' => ['message' => '邮件接收成功!']];
        }catch (\Exception $e){
            return ['code' => 1004, 'data' => ['message' => '邮件接收失败!'.$e->getMessage()]];
        }
    }
    function ReCoverImapGarbled($str,$encoding,$charset){
        if($str == ''){
            return $str;
        }
        if($encoding == 3) {
            if($charset != 'UTF-8') {
                $email_content = iconv($charset, 'UTF-8', imap_base64($str));
            }else{
                $email_content = imap_base64($str);
            }
        } else if($encoding == 4) {
            if($charset != 'UTF-8') {
                $email_content = iconv($charset, 'UTF-8', imap_qprint($str));
            }else{
                $email_content = imap_qprint($str);
            }
        } else
        {
            if($charset != 'UTF-8') {
                $email_content = iconv($charset, 'UTF-8', $str);
            }else{
                $email_content = $str;
            }
        }
        return $email_content;
    }
}
