<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
}
