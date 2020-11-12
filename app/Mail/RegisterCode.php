<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class RegisterCode extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        //
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        /*$code = rand(1000,9999);
        return $this->from('admin@phpartisan.cn','Laravel学习网')->subject("验证码 CAPTCHA")->view('emails.register')->with([
            'email' => $this->email,
            'code' => $code,
            'img' => "https://www.baidu.com/img/PCtm_d9c8750bed0b3c7d089fa7d55720d6cf.png",
        ]);*/
        return $this->view('emails.register')
            ->with([
                'orderName' => 123,
                'orderPrice' => 456,
            ]);
        //return $this->view('view.name');
    }
}
