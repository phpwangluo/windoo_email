<?php

namespace App\Http\Controllers\Emails;

use App\Exports\Export;
use App\Http\Controllers\Controller;
use App\Models\MailSended;
use Illuminate\Http\Request;

use App\Mail\ContactSender;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Sender;
use App\Models\MailForSend;
use League\Flysystem\Config;
use \Exception;
use Illuminate\Support\Facades\Response;

use Maatwebsite\Excel\Facades\Excel;

class ExportSenderTemplateController extends Controller
{
    public function export(Request $request)
    {
        //设置表头
        $row = [
            [
            "support_name"=>'运营商',
            "email_address"=>'邮箱地址',
            "email_pass"=>'邮箱密码',
            "email_sign"=>'邮箱签名',
            "remarks"=>'备注']
        ];

        //数据
        $list=[
        ];
        //导出
        $data = $list;//要导入的数据
        $header = $row;//导出表头
        $excel = new Export($data, $header,'');
        return Excel::download($excel, 'sender.xlsx');
    }
}
