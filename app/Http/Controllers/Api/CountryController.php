<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;

class CountryController extends Controller
{
    //异步获取下拉菜单国家列表
    public function countryList(Request $request)
    {
        $q = $request->get('q');
        $country_data = Country::where('country_name', 'like', "%$q%")
            ->where('status','=',1)->paginate(null, ['id', 'country_name as text'])->toArray();
        return $country_data['data'];
    }
}
