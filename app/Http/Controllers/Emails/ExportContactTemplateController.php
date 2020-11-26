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
        $row = [
            [
                "0"=>'联系人邮箱地址',
                "1"=>'联系人所属国家',
                "2"=>'联系人所属行业',
                "3"=>'联系人模板',
                "4"=>'示例项目',
                "5"=>'联系人标发送邮件开始时间点',
                "6"=>'发送邮件结束时间点',
                "7"=>'备注'
            ]
        ];

        //数据
        $list=[
        ];
        //导出
        $data = $list;//要导入的数据
        $header = $row;//导出表头
        $excel = new Export($data, $header,'contact');
        return Excel::download($excel, "contact.xlsx");
    }
}
