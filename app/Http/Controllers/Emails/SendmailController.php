<?php

namespace App\Http\Controllers\Emails;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\RegisterCode;

use Illuminate\Support\Facades\Mail;

class SendmailController extends Controller
{
    //测试邮件发送
    /*public function test(Request $request){
        # 收件人邮箱
        $email = '1617049813@qq.com';
        Mail::to($email)->send(new RegisterCode($email));
    }*/
    //发订阅邮件
    public function test(Request $request)
    {
        $user = 'zhangshaojie';
        //发邮件
        dd(Mail::to('1617049813@qq.com')->send(new RegisterCode($user)));
    }
}
