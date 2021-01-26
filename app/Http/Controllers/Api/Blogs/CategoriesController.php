<?php

namespace App\Http\Controllers\Api\Blogs;

use App\Http\Controllers\Controller;
use App\Models\SitesBlogCategories;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    //异步获取下拉菜单国家列表
    public function categoryList(Request $request)
    {
        $q = $request->get('q');
        $site_id = $request->get('site_id');
        $obj = SitesBlogCategories::query()->where('name', 'like', "%$q%")
            ->where(['status'=>1,'type'=>1]);
        if($site_id){
            $obj->where(['site_id'=>$site_id]);
        }
        $data = $obj->paginate(null, ['id', 'name as text'])->toArray();
        return $data['data'];
    }
}
