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

class ExportContactTemplateController extends Controller
{
    public function export(Request $request)
    {
        //设置表头
        $header = [
            [
                "0"=>'联系人邮箱地址（必填）',
                "1"=>'联系人所属国家（必填）',
                "2"=>'联系人所属行业（必填）',
                "3"=>'联系人模板',
                "4"=>'项目（必填）',
                "5"=>'联系人标发送邮件开始时间点',
                "6"=>'发送邮件结束时间点',
                "7"=>'备注'
            ]
        ];
        $columnWidth = [
            "A"=>30,
            "B"=>30,
            "C"=>30,
            "D"=>30,
            "E"=>20,
            "F"=>30,
            "J"=>30,
            "H"=>30
        ];
        $excel = new Export([], $header,'收件箱联系人导入模板.xlsx',$columnWidth);
        return Excel::download($excel, "收件箱联系人导入模板.xlsx",'Xlsx');
    }
}
