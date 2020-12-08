<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    //异步获取下拉菜单联系人列表
    public function contactList(Request $request)
    {
        $q = $request->get('q');
        $country_data = Contact::where('email_address', 'like', "%$q%")
            ->where('status','=',1)->paginate(null, ['id', 'email_address as text'])->toArray();
        return $country_data['data'];
    }

    /**
     * 修改联系人状态
     * @param Request $request
     * @return array
     */
    public function changeStatus(Request $request)
    {
        $id = $request->post('id');
        $task_status = $request->post('task_status');
        if(!$id){
            return [
                'error'=>0,'message'=>'参数不合法'
            ];
        }
        //当启用联系人的时候验证联系人的必要条件是否满足
        if($task_status == 1){
            $contact_detail_obj = Contact::where([
                'id'=>$id
            ])->get()->first();
            if($contact_detail_obj){
                $contact_detail = $contact_detail_obj->toArray();
                if($contact_detail['template_id'] == ''){
                    return [
                        'error'=>0,'message'=>'联系人未绑定模板不能启用'
                    ];
                }
                if($contact_detail['send_max_num'] <= 0){
                    return [
                        'error'=>0,'message'=>'联系人发送次数已用完请修改联系人信息以后再启用'
                    ];
                }
            }
        }
        Contact::where('id',$id)
            ->update([
                'task_status'=>$task_status,
            ]);
        return [
            'error'=>1,'message'=>'成功'
        ];
    }
}
