<?php

namespace App\Http\Controllers\Emails;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\MailForSend;
use App\Models\MailReceived;
use App\Models\Sender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Webklex\IMAP\Facades\Client;
use App\Exceptions\Diy\EmailException;

class GetmailController extends Controller
{
    //获取某个邮箱的邮件内容

    public function getEmail(Request $request){

        //拉取邮件的业务逻辑
        try{
            //获取有效的可以拉取邮件的邮箱
            $mail = Sender::join('mail_settings','mail_settings.id','=','senders.mail_setting_id')
                ->where([
                    'status'=>1,
                    'email_status'=>1
                ])
                ->whereRaw('send_count<=max_send_count')
                ->inRandomOrder()
                ->get()->toArray();
            foreach ($mail as $k=>$v){
                $config = [
                    'host'          => $v['getmail_host'],
                    'port'          => $v['getmail_port'],
                    'encryption'    => $v['getmail_encryption'],
                    //'validate_cert' => false,
                    'username'      => $v['email_address'],
                    'password'      => $v['email_pass'],
                    'protocol'      => $v['getmail_protocol'],
                ];
                request()->offsetSet('data', $config);
                /** @var \Webklex\PHPIMAP\Client $client */

                $client = Client::make($config);
                //创建连接
                /* Alternative by using the Facade
                $client = Webklex\IMAP\Facades\Client::account('default');
                */
                //Connect to the IMAP Server;
                $client->connect()->setTimeout(1000);
                //获取收件箱
                //Get all Mailboxes
                /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
                $folders = $client->getFolders(false,'INBOX');
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
                        $content = $message->getStructure()->parts[2]->content;
                        $encoding = $message->getStructure()->parts[2]->encoding;
                        $charset = $message->getStructure()->parts[2]->charset;
                        if($charset != 'utf-8' && $charset != 'gb2312' && $charset != 'gbk'){
                            $title = iconv($charset,'utf-8//IGNORE',$message->getSubject());
                        }else{
                            $title = $message->getSubject();
                        }
                        $email_content = $this->ReCoverImapGarbled($content,$encoding,$charset);
                        //对拉取的邮箱结果做判断，过滤不是系统发出去的邮件的回复
                        $is_from_gp_email_reply = Contact::where([
                            'email_address'=>$message->getSender()[0]->mail,
                            'status'=>1
                        ])->get()->toArray();
                        if(empty($is_from_gp_email_reply)){
                            continue;
                        }
                        $reply_unseen[$kk]['sender_email'] = $is_from_gp_email_reply[0]['email_address'] ? $is_from_gp_email_reply[0]['email_address'] :$message->getSender()[0]->mail;
                        //$reply_unseen[$kk]['sender_email'] = $message->getSender()[0]->mail;
                        $reply_unseen[$kk]['receiver_email'] = $message->getTo()[0]->mail;
                        $reply_unseen[$kk]['title'] = $title;
                        $reply_unseen[$kk]['content'] = $email_content != '' ? $email_content : ($message->getHTMLBody() ? $message->getHTMLBody() : $this->code_to_string($message->getTextBody()));
                        $reply_unseen[$kk]['receive_time'] = date('Y-m-d H:i:s',$message->getDate()->toDate()->getTimestamp());
                        $reply_unseen[$kk]['created_at'] = date('Y-m-d H:i:s',time());

                        //对拉取的回复做去重验证
                        $is_repeat_reply = MailReceived::query()->where([
                            'sender_email'=>$reply_unseen[$kk]['sender_email'],
                            'receiver_email'=>$reply_unseen[$kk]['receiver_email'],
                            'title'=>$reply_unseen[$kk]['title'],
                            'content'=>$reply_unseen[$kk]['content']
                        ])->exists();
                        if($is_repeat_reply){
                            unset($reply_unseen[$kk]);
                        }
                    }
                    if(!empty($reply_unseen)){
                        MailReceived::insert($reply_unseen);
                        //将收到回复的联系人的状态变更成停用
                        $batch_update_contact = [];
                        foreach ($reply_unseen as $ck =>$cv){
                            $batch_update_contact[$ck]['email_address'] = $cv['sender_email'];
                            $batch_update_contact[$ck]['task_status'] = 0;
                            //$batch_update_contact[$ck]['updated_at'] = date('Y-m-d H:i:s',time());
                        }
                        $firstRow = current($batch_update_contact);
                        $updateColumn = array_keys($firstRow);
                        $referenceColumn =  current($updateColumn);
                        unset($updateColumn[0]);
                        // 拼接sql语句
                        $updateSql = "UPDATE contacts SET ";
                        $sets = [];
                        $bindings = [];
                        foreach ($updateColumn as $uColumn) {
                            $setSql = "`" . $uColumn . "` = CASE ";
                            foreach ($batch_update_contact as $data) {
                                $setSql .= "WHEN `" . $referenceColumn . "` = ? THEN ? ";
                                $bindings[] = $data[$referenceColumn];
                                $bindings[] = $data[$uColumn];
                            }
                            $setSql .= "ELSE `" . $uColumn . "` END ";
                            $sets[] = $setSql;
                        }
                        $updateSql .= implode(', ', $sets);
                        $whereIn = collect($batch_update_contact)->pluck($referenceColumn)->values()->all();
                        $bindings = array_merge($bindings, $whereIn);
                        $whereIn = rtrim(str_repeat('?,', count($whereIn)), ',');
                        $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ")";
                        DB::update($updateSql, $bindings);
                        //更新发件人收到的回复数量
                        $sender_emails = array_unique(array_column($reply_unseen,'receiver_email'));
                        Sender::whereIn('email_address',$sender_emails)->increment('receive_count');
                    }
                }
            }
            return 1;
        }catch (\Exception $e){
            $message = '拉取邮件失败:'.$e->getMessage();
            Log::channel('error_gp_email')->error($message, [$request->get('mail_data')]);
            throw new EmailException($message);
        }
    }
    /**
     * 换行符等转换成html格式输出
     **/
    function code_to_string($str)
    {
        /*
        * \t:水平制表（跳到下一个Tab位置）意思是按一个tab
        * \n:换行
        * \r:回车，将当前位置移到本行开头
        */
        $pre = array("\t" , "\n" , "\r" );
        $to = array( '<br>' , '<br>' , '<br>' );
        return str_replace ( $pre , $to , $str );
    }
    function ReCoverImapGarbled($str,$encoding,$charset){
        if($str == ''){
            return $str;
        }
        if($encoding == 3) {
            if($charset != 'UTF-8') {
                $email_content = iconv($charset, 'UTF-8//IGNORE', imap_base64($str));
            }else{
                $email_content = imap_base64($str);
            }
        } else if($encoding == 4) {
            if($charset != 'UTF-8') {
                $email_content = iconv($charset, 'UTF-8//IGNORE', imap_qprint($str));
            }else{
                $email_content = imap_qprint($str);
            }
        } else
        {
            if($charset != 'UTF-8') {
                $email_content = iconv($charset, 'UTF-8//IGNORE', $str);
            }else{
                $email_content = $str;
            }
        }
        return $email_content;
    }
}
