<?php

namespace App\Admin\Actions\Diy;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class MailReceivedDetailAction extends RowAction
{
    public $name = '回复记录';

//    public function handle(Model $model, Request $request)
//    {
//        // 获取到表单中的`type`值
//        $request->get('type');
//
//        // 获取表单中的`reason`值
//        $request->get('reason');
//
//        // 你的举报逻辑...
//
//        return $this->response()->success('合作意向已保存')->refresh();
//    }

    /**
     * @return  string
     */
    public function href()
    {
        return $this->getResource().'/'.$this->getKey();
    }

    /*public function form()
    {
        $type = [
            1 => '不合作',
            2 => '合作中',
            3 => '已合作',
        ];
        $this->html('sds','<a href="sdfds">sdfsdfsdf</a>');
        $this->radio('type', '合作意向')->options($type);
        $this->text('dsf','<a href="sdfds">sdfsdfsdf</a>');
        $this->textarea('reason', '邮件列表')->rules('required')->render('admins/home');
    }*/
}
