<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use App\Models\Trade;
class TradeController extends Controller
{
    //异步获取下拉菜单行业列表
    public function tradeList(Request $request)
    {
        $q = $request->get('q');
        $trade_arr = Trade::where('trade_name', 'like', "%$q%")
            ->where('status','=',1)->paginate(null, ['id', 'trade_name as text'])->toArray();
        return $trade_arr['data'];
    }

    /**
     * 异步删除行业
     * @param Request $request
     * @return array
     */
    public function deleteTrade(Request $request){
        $id = $request->post('id');
        if(!$id){
            return [
                'error'=>0,'message'=>'参数不合法'
            ];
        }
        //判断是否在模板中被使用
        $isExistInTemplate = Template::where([
           'trade_id'=>$id,
           'status'=>1
        ])->get()->first();
        if($isExistInTemplate->id){
            return [
                'error'=>0,'message'=>'行业被模板占用，不可删除！'
            ];
        }
        Trade::where('id',$id)
            ->update([
                'status'=>0,
            ]);
        return [
            'error'=>1,'message'=>'成功'
        ];
    }
}
