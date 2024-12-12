<?php

namespace App\Admin\Actions\Diy;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ChangeSendStatusAction extends RowAction
{
    public $name = '取消发送';
    public function __construct($newname='')
    {
        $this->name = $newname;
    }

    public function handle(Model $model)
    {
        // $request ...
        if($model->send_status == 1){
            $model->update(['send_status'=>3],['id'=>$model->id]);
        }elseif($model->send_status == 3){
            $model->update([
                'send_status'=>1,
                'send_type'=>2,
                'plan_send_time'=>date('Y-m-d H:i:s',time())
            ],['id'=>$model->id]);
        }
        return $this->response()->success('操作成功.')->refresh();
    }

}
