<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MailForSend;
use Illuminate\Http\Request;

class MailForSendController extends Controller
{
    /**
     * 修改邮件任务状态
     * @param Request $request
     * @return array
     */
    public function changeMailForSendStatus(Request $request)
    {
        $id = $request->post('id');
        $send_status = $request->post('send_status');
        if(!$id){
            return [
                'error'=>0,'message'=>'参数不合法'
            ];
        }
        // $request ...
        if($send_status == 1){
            MailForSend::where('id',$id)
                ->update([
                    'send_status'=>$send_status,
                    'send_type'=>2,
                    'plan_send_time'=>date('Y-m-d H:i:s',time())
                ]);
        }elseif($send_status == 3){
            MailForSend::where('id',$id)
                ->update([
                    'send_status'=>$send_status,
                ]);
        }
        return [
            'error'=>1,'message'=>'成功'
        ];
    }
}
