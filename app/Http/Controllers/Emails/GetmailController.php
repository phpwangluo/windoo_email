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
                    $messages = $folder->messages()->on('2020-11-26')->all()->get();
                    //$messages = $folder->messages()->unseen()->on('2020-11-25')->all()->get();
                    //$messages = $folder->messages()->unseen()->all()->get();
                    $reply_unseen = [];
                    foreach($messages as $k => $message){
                        $charset = '';
                        $charset = $message->getStructure()->parts[0]->charset;
                        $reply_unseen[$k]['sender_email'] = $message->getSender()[0]->mail;
                        $reply_unseen[$k]['receiver_email'] = $message->getTo()[0]->mail;
                        $reply_unseen[$k]['title'] = iconv($charset,'utf8',$message->getSubject());
                        $reply_unseen[$k]['content'] = $message->getHTMLBody(true);
                        $reply_unseen[$k]['receive_time'] = date('Y-m-d H:i:s',$message->getDate()->toDate()->getTimestamp());
                        $reply_unseen[$k]['created_at'] = date('Y-m-d H:i:s',time());
                    }
                    dd($messages);
                    if(!empty($reply_unseen)){
                        MailReceived::insert($reply_unseen);
                    }
                }
            }
            return ['code' => 1000, 'data' => ['message' => '邮件发送成功!']];
        }catch (\Exception $e){
            return ['code' => 1004, 'data' => ['message' => '邮件发送失败!'.$e->getMessage()]];
        }
        dd('sdfsdf');
        /** @var \Webklex\PHPIMAP\Client $client */
        /*$client = Client::make([
            'host'          => 'outlook.com',
            'port'          => 993,
            'encryption'    => 'ssl',
            'validate_cert' => true,
            'username'      => 'z1617049813@outlook.com',
            'password'      => '*zs1617049813sj',
            'protocol'      => 'imap'
        ]);*/
        /* Alternative by using the Facade
        $client = Webklex\IMAP\Facades\Client::account('default');
        */
        //Connect to the IMAP Server
        $client->connect();

        //Get all Mailboxes
        /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
        $folders = $client->getFolders();
        //Loop through every Mailbox
        /** @var \Webklex\PHPIMAP\Folder $folder */
        foreach($folders as $folder){
            if($folder->name != 'Inbox'){
                continue;
            }
            //Get all Messages of the current Mailbox $folder
            /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */

            //$messages = $folder->messages()->all()->get();
            $messages = $folder->messages()->on('2020-11-24')->all()->get();
            //$messages = $folder->messages()->unseen()->on('2020-11-24')->all()->get();
            /** @var \Webklex\PHPIMAP\Message $message */
            $reply_unseen = [];
            foreach($messages as $k => $message){
                //$arr[] = $message->getFrom()[0]->mail;
                //dd(iconv($message->getSubject(),'utf8','gbk'));
                $reply_unseen[$k]['sender_email'] = $message->getSender()[0]->mail;

                dd($reply_unseen);
                //echo 'Attachments: '.$message->getAttachments()->count().'<br />';
                //echo $message->getHTMLBody();
                //Move the current Message to 'INBOX.read'
                if($message->move('INBOX.read') == true){
                    echo 'Message has ben moved';
                }else{
                    echo 'Message could not be moved';
                }
            }
        }
    }
}
