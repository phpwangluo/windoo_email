<?php

namespace App\Http\Controllers\Emails;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GetmailController extends Controller
{
    //获取某个邮箱的邮件内容

    public function getEmail(Request $request){
        /** @var \Webklex\PHPIMAP\Client $client */
        $client = \Webklex\IMAP\Facades\Client::make([
            'host'          => 'outlook.com',
            'port'          => 993,
            'encryption'    => 'ssl',
            'validate_cert' => true,
            'username'      => 'z1617049813@outlook.com',
            'password'      => '*zs1617049813sj',
            'protocol'      => 'imap'
        ]);
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

            $messages = $folder->messages()->all()->get();
            //messages = $folder->messages()->on('2020-11-12')->all()->get();
            //$messages = $folder->messages()->unseen()->on('2020-11-11')->all()->get();
            /** @var \Webklex\PHPIMAP\Message $message */
            $arr = [];
            foreach($messages as $message){
                //echo $message->getSubject().'<br />';
                $arr[] = $message->getFrom()[0]->mail;

                //echo 'Attachments: '.$message->getAttachments()->count().'<br />';
                //echo $message->getHTMLBody();
                //Move the current Message to 'INBOX.read'
                /*if($message->moveToFolder('INBOX.read') == true){
                    echo 'Message has ben moved';
                }else{
                    echo 'Message could not be moved';
                }*/
            }
            $arr_format = array_unique($arr);
            dd($arr_format);
        }
    }
}
