<?php

namespace App\Http\Controllers\Api\Blogs;

use App\Http\Controllers\Controller;
use App\Models\SitesBlogCategories;
use Illuminate\Http\Request;
use App\Models\Country;

class CategoriesController extends Controller
{
    //异步获取下拉菜单国家列表
    public function categoryList(Request $request)
    {
        $q = $request->get('q');
        $data = SitesBlogCategories::where('name', 'like', "%$q%")
            ->where(['status'=>1,'type'=>1])->paginate(null, ['id', 'name as text'])->toArray();
        return $data['data'];
    }
}
