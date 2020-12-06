<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessSource;
use App\Models\Contact;
use App\Models\MailReceived;
use Illuminate\Http\Request;

class MailReceivedController extends Controller
{
    /**
     * 修改合作意向状态
     * @param Request $request
     * @return array
     */
    public function changeBusinessStatus(Request $request)
    {
        $business_status = $request->post('business_status');
        $remarks = $request->post('remarks');
        $id = $request->post('id');
        if(!$id && !$business_status){
            return [
                'error'=>0,
                'message'=>'参数不正确'
            ];
        }
        //生成或者更新合作人合作意向
        //查看合作资源表中是否有对应的联系人邮箱
        //获取当前联系人的邮箱地址
        $mail_received_detial = MailReceived::where([
            'id'=>$id
        ])->first();
        if(!$mail_received_detial){
            return [
                'error'=>0,
                'message'=>'没有找到对应的联系人信息'
            ];
        }
        $is_exist_source = BusinessSource::where([
            'email_address'=>$mail_received_detial->sender_email,
        ])->get()->toArray();
        $contact_detail = Contact::where([
            'email_address'=>$mail_received_detial->sender_email,
        ])->first()->toArray();
        if(empty($is_exist_source)){
            //写入合作资源表
            $insert_data = [
                'email_address'=>$mail_received_detial->sender_email,
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
            BusinessSource::where('email_address',$mail_received_detial->sender_email)
                ->update([
                    'business_status'=>$business_status,
                    'remarks'=>$remarks,
                ]);

        }
        //把邮件状态变成已处理
        MailReceived::where('id',$id)
            ->update([
                'receive_status'=>2,
            ]);
        return [
            'error'=>1,'message'=>'成功'
        ];
    }
}
