<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MailSetting;
use Illuminate\Http\Request;
class MailsettingController extends Controller
{
    /*
     * 获取同一运营商的所有备用配置
     */
    public function samesettingList(Request $request)
    {
        $q = $request->get('q');
        $prefix = $request->get('prefix');
        $like = $prefix.$q;
        $arr = MailSetting::where('support_name', 'like', "%$like%")->paginate(null, ['id', 'support_name as text'])->toArray();
        return $arr['data'];
    }
}
