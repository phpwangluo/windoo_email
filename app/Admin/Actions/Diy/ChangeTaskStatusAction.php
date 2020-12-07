<?php

namespace App\Admin\Actions\Diy;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ChangeTaskStatusAction extends RowAction
{
    public $name = '停用';
    public function __construct($newname='')
    {
        $this->name = $newname;
    }

    public function handle(Model $model)
    {
        // $request ...
        if($model->task_status == 1){
            $model->update(['task_status'=>0],['id'=>$model->id]);
            /*MailForSend::where('receiver_email',$model->email_address)
                ->update(['send_status' => 2]);*/
        }else{
            $model->update(['task_status'=>1],['id'=>$model->id]);
            /*MailForSend::where('receiver_email',$model->email_address)
                ->update(['send_status' => 1]);*/
        }
        return $this->response()->success('操作成功.')->refresh();
    }

}
