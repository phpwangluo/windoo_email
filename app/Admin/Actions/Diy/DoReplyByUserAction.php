<?php

namespace App\Admin\Actions\Diy;

use Encore\Admin\Actions\RowAction;
class DoReplyByUserAction extends RowAction
{
    public $name = '回复';

    /*public function handle(Model $model, Request $request)
    {
        //回去回复邮件内容，并写入发送邮件任务列表
        $business_status = $request->get('business_status');

        //把邮件状态变成已处理
        MailReceived::where('id',$model->id)
            ->update([
                'receive_status'=>2,
            ]);
        return $this->response()->success('回复成功')->refresh();
    }

    public function form(Model $model)
    {
        $this->text('receiver_email','发件箱')->default($model->sender_email)->readonly();
        $this->text('title','标题')->rules('required');
        $this->textarea('content', '内容')->rules('required');
        $this->text('email_sign', '签名')->rules('required');
    }*/
    /**
     * @return  string
     */
    public function href()
    {
        return $this->getResource().'/'.$this->getKey().'/edit';
    }
}
