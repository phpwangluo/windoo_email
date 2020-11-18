<?php

namespace App\Admin\Actions\Diy;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ChangeTaskStatusAction extends RowAction
{
    public $name = '停用';

    public function handle(Model $model)
    {
        // $request ...
        $model->update(['status'=>0],['id'=>$model->id]);
        return $this->response()->success('操作成功.')->refresh();
    }

}
