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
                "0"=>'联系人（必填，如：123@qq.com）',
                "1"=>'国家（必填，如：美国）',
                "2"=>'行业（必填，如：汽车）',
                "3"=>'模板名称（选填）',
                "4"=>'项目（必填）',
                "5"=>'开始时间（选填）',
                "6"=>'结束时间（选填）',
                "7"=>'备注（选填）'
            ]
        ];
        $columnWidth = [
            "A"=>40,
            "B"=>30,
            "C"=>30,
            "D"=>30,
            "E"=>20,
            "F"=>30,
            "G"=>30,
            "H"=>30
        ];
        $excel = new Export([], $header,'收件箱联系人导入模板.xlsx',$columnWidth);
        return Excel::download($excel, "收件箱联系人导入模板.xlsx",'Xlsx');
    }
}
