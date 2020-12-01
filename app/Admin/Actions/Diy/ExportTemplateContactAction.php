<?php

namespace App\Admin\Actions\Diy;
use Encore\Admin\Actions\RowAction;
class ExportTemplateContactAction extends RowAction
{
    public $name = <<<HTML
        <a target="_blank" href="/export_contact_template" class="btn btn-sm btn-default import-action">下载导入模板</a>
HTML;
    /**
     * @return  string
     */
    public function href()
    {
        return url('/export_contact_template');
    }
}
