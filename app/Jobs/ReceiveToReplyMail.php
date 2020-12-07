<?php

namespace App\Jobs;

use App\Models\MailForSend;
use App\Models\MailReceived;
use App\Models\Sender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                        if($charset != 'utf-8' && $charset != 'gb2312' && $charset != 'gbk'){
                            $title = iconv($charset,'utf-8//IGNORE',$message->getSubject());
                        }else{
                            $title = $message->getSubject();
                        }
                        $email_content = $this->ReCoverImapGarbled($content,$encoding,$charset);
                        //对拉取的邮箱结果做判断，过滤不是系统发出去的邮件的回复
                        $is_from_gp_email_reply = MailForSend::where([
                            'receiver_email'=>$message->getSender()[0]->mail,
                            'sender_email'=>$message->getTo()[0]->mail,
                            'send_status'=>2
                        ])->get()->toArray();
                        if(empty($is_from_gp_email_reply)){
                            continue;
                        }
                        $reply_unseen[$kk]['sender_email'] = $message->getSender()[0]->mail;
                        $reply_unseen[$kk]['receiver_email'] = $message->getTo()[0]->mail;
                        $reply_unseen[$kk]['title'] = $title;
                        $reply_unseen[$kk]['content'] = $email_content != '' ? $email_content : ($message->getHTMLBody() ? $message->getHTMLBody() : $this->code_to_string($message->getTextBody())) ;
                        $reply_unseen[$kk]['receive_time'] = date('Y-m-d H:i:s',$message->getDate()->toDate()->getTimestamp());
                        //$reply_unseen[$kk]['created_at'] = date('Y-m-d H:i:s',time());
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
            return ['code' => 1000, 'data' => ['message' => '邮件接收成功!']];
        }catch (\Exception $e){
            $message = '拉取邮件失败';
            Log::channel('error_gp_email')->error($message, $e->getMessage());
            return ['code' => 1004, 'data' => ['message' => '邮件接收失败!'.$e->getMessage()]];
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
        $pre = array( " " , "　" , "\t" , "\n" , "\r" );
        $to = array( '&nbsp;' , '&nbsp;&nbsp;' , '&nbsp;&nbsp;&nbsp;&nbsp;' , '<br>' , '<br>' );
        return str_replace ( $pre , $to , $str );
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
