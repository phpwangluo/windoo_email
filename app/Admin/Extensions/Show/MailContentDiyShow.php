<?php


namespace App\Admin\Extensions\Show;

use App\Models\MailForSend;
use Encore\Admin\Show\AbstractField;

class MailContentDiyShow extends AbstractField
{
    public function render($id='')
    {
        $this->escape=false;
        $mail_for_send = MailForSend::where([
            'id'=>$id
        ])->first()->toArray();
        return $mail_for_send['content'];
    }
}
