<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterCode extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $view = 'emails.register';

    public $subject = '欢迎注册';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $viewData = [], $view = '')
    {
        //
        if(!empty($subject)){
            $this->subject($subject);
        }

        if(!empty($view)){
            $this->view = $view;
        }

        if(! view()->exists($this->view)){
            abort(404, '注册邮件模板不存在');
        }

        if(!empty($viewData)){
            $this->with($viewData);
        }
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
      /*  return $this->view('emails.register')
            ->with([
                'orderName' => 123,
                'orderPrice' => 456,
            ]);*/
        //return $this->view('view.name');

        return $this->markdown($this->view);
    }
}
