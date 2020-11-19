<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactSender extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $view = 'emails.contact';
    public $subject = 'Dears';
    public $content = '';
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

        if(!view()->exists($this->view)){
            abort(404, '邮件模板不存在');
        }

        if(!empty($viewData)){
            $this->with($viewData);
            $this->content = $viewData['content'];
        }

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //return $this->view($this->view);
        //return $this->markdown($this->view);
        return $this->html(
            $this->content
        );
    }
}
