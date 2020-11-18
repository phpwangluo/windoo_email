<?php

namespace App\Admin\Actions\Diy;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class NewDelete extends RowAction
{
    public $name = '删除';

    public function handle(Model $model)
    {
        //dd($model);
        $model->update(['status'=>0],['id'=>$model->id]);
        return $this->response()->success('操作成功.')->refresh();
    }

}
