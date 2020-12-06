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
        Contact::where('id',$id)
            ->update([
                'task_status'=>$task_status,
            ]);
        return [
            'error'=>1,'message'=>'成功'
        ];
    }
}
