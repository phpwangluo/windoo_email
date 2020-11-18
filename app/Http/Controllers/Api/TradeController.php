<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trade;
class TradeController extends Controller
{
    //异步获取下拉菜单行业列表
    public function tradeList(Request $request)
    {
        $q = $request->get('q');
        $trade_arr = Trade::where('trade_name', 'like', "%$q%")->paginate(null, ['id', 'trade_name as text'])->toArray();
        return $trade_arr['data'];
    }
}
