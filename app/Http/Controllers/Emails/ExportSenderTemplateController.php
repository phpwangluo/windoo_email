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
        $header = [
            [
                "email_address"=>'发件人(必填比如：123@outlook.com)',
                "email_pass"=>'密码（必填）',
                "email_sign"=>'签名（选填）',
                "remarks"=>'备注（选填）'
            ]
        ];
        $columnWidth = [
            "A"=>50,
            "B"=>30,
            "C"=>30,
            "D"=>30
        ];
        $excel = new Export([], $header,'发件箱导入模板.xlsx',$columnWidth);
        return Excel::download($excel, '发件箱导入模板.xlsx','Xlsx');
    }
}
