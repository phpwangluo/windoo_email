<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Models\Template;
class TemplateController extends Controller
{
    //异步获取下拉菜单模板列表
    public function templateList(Request $request)
    {
        $q = $request->get('q');
        $trade_arr = Template::where('template_name', 'like', "%$q%")
            ->where('status','=',1)->paginate(null, ['id', 'template_name as text'])->toArray();
        return $trade_arr['data'];
    }

    /**
     * 异步删除模板
     * @param Request $request
     * @return array
     */
    public function deleteTemplate(Request $request){
        $id = $request->post('id');
        if(!$id){
            return [
                'error'=>0,'message'=>'参数不合法'
            ];
        }
        //判断是否在有效的联系人中被使用
        $isExistInContact = Contact::where([
            'template_id'=>$id,
            'status'=>1,
            'task_status'=>1
        ])->get()->first();
        if($isExistInContact->id){
            return [
                'error'=>0,'message'=>'模板被有效联系人占用，不可删除！'
            ];
        }
        Template::where('id',$id)
            ->update([
                'status'=>0,
            ]);
        return [
            'error'=>1,'message'=>'成功'
        ];
    }
}
