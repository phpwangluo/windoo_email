<?php

namespace App\Admin\Actions\Diy;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class MailReceivedDetailAction extends RowAction
{
    public $name = '查看';

    /*public function handle(Model $model)
    {
        return $this->render('/1');
    }*/

    /**
     * @return  string
     */
    public function href()
    {
        return $this->getResource().'/'.$this->getKey();
    }
}
