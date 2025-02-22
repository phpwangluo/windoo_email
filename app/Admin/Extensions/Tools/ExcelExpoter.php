<?php

namespace App\Admin\Extensions\Tools;

use App\Models\Sender;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExpoter extends AbstractExporter
{
    protected $file_name = 'file';
    protected $sheet_name = 'sheet';
    protected $head = [];
    protected $body = [];

    public function setAttr($file_name, $sheet_name, $head, $body)
    {
        $this->file_name = $file_name;
        $this->sheet_name = $sheet_name;
        $this->head = $head;
        $this->body = $body;
    }

    public function export()
    {
        Excel::download(Sender::class,'fsdf.xls');
        Excel::create($this->file_name, function($excel) {
            $excel->sheet($this->sheet_name, function($sheet) {
                // 这段逻辑是从表格数据中取出需要导出的字段
                $body = $this->body;
                $bodyRows = collect($this->getData())->map(function ($item) use($body) {
                    $arr = [];

                    foreach($body as $value) {
                        $arr[] = array_get($item, $value);
                    }

                    return $arr;
                });
                $rows = collect([$this->head])->merge($bodyRows);
                $sheet->rows($rows);
            });
        })->export('xls');//.xls .csv ...
    }
}
