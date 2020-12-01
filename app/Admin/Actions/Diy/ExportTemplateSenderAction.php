<?php

namespace App\Admin\Actions\Diy;
use Encore\Admin\Actions\RowAction;
class ExportTemplateSenderAction extends RowAction
{
    public $name = <<<HTML
        <a target="_blank" href="/export_sender_template" class="btn btn-sm btn-default import-action">下载导入模板</a>
HTML;
    /**
     * @return  string
     */
    public function href()
    {
        return url('/export_sender_template');
    }
}
