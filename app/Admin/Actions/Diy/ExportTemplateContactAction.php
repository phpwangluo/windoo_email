<?php

namespace App\Admin\Actions\Diy;
use Encore\Admin\Actions\RowAction;
class ExportTemplateContactAction extends RowAction
{
    public $name = <<<HTML
        <a class="btn btn-sm btn-default import-action" href="/export_contact_template">下载导入模板</a>
HTML;
    /**
     * @return  string
     */
    public function href()
    {
        return url('/export_contact_template');
    }
}
