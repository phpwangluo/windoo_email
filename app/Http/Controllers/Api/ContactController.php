<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Models\Country;

class ContactController extends Controller
{
    //异步获取下拉菜单联系人列表
    public function contactList(Request $request)
    {
        $q = $request->get('q');
        $country_data = Contact::where('email_address', 'like', "%$q%")->paginate(null, ['id', 'email_address as text'])->toArray();
        return $country_data['data'];
    }
}
