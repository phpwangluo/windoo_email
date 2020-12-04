<?php

namespace App\Admin\Actions\Diy;

use App\Models\BusinessSource;
use App\Models\Contact;
use App\Models\MailReceived;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
class ChangeBusinessStatusAction extends RowAction
{
    public $name = '合作意向';

    public function handle(Model $model, Request $request)
    {
        $business_status = $request->get('business_status');
        $remarks = $request->get('remarks');
        //生成或者更新合作人合作意向
        //查看合作资源表中是否有对应的联系人邮箱
        $is_exist_source = BusinessSource::where([
            'email_address'=>$model->sender_email,
        ])->get()->toArray();
        $contact_detail = Contact::where([
            'email_address'=>$model->sender_email,
        ])->first()->toArray();
        if(empty($is_exist_source)){
            //写入合作资源表
            $insert_data = [
                'email_address'=>$model->sender_email,
                'country_id'=>$contact_detail['country_id'],
                'trade_id'=>$contact_detail['trade_id'],
                'customer_tag'=>$contact_detail['customer_tag'],
                'business_status'=>$business_status,
                'remarks'=>$remarks,
                'created_at'=>date('Y-m-d H:i:s',time()),
            ];
            BusinessSource::insert($insert_data);
        }else{
            //更新合作资源
            BusinessSource::where('email_address',$model->sender_email)
                ->update([
                    'business_status'=>$business_status,
                    'remarks'=>$remarks,
                ]);

        }
        //把邮件状态变成已处理
        MailReceived::where('id',$model->id)
            ->update([
                'receive_status'=>2,
            ]);
        return $this->response()->success('合作意向已保存')->refresh();
    }

    public function form()
    {
        $type = [
            0 => '不合作',
            1 => '合作中',
            2 => '已合作',
        ];
        $this->radio('business_status', '合作意向')->options($type);
        $this->textarea('remarks', '意向备注')->rules('required');
    }
}
