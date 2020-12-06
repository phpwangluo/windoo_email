<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sender;
use Illuminate\Http\Request;
class SenderController extends Controller
{
    /**
     * 异步删除行业
     * @param Request $request
     * @return array
     */
    public function deleteSender(Request $request){
        $id = $request->post('id');
        if(!$id){
            return [
                'error'=>0,'message'=>'参数不合法'
            ];
        }
        //判断发件人状态是否启用，启用的联系人不可被删除
        $isOnUse = Sender::where([
           'id'=>$id,
           'email_status'=>1
        ])->get()->first();
        if($isOnUse->id){
            return [
                'error'=>0,'message'=>'发件人处于启用状态，不可删除！'
            ];
        }
        Sender::where('id',$id)
            ->update([
                'status'=>0,
            ]);
        return [
            'error'=>1,'message'=>'成功'
        ];
    }
}
