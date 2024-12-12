<?php

namespace App\Admin\Actions\Diy;
use Encore\Admin\Actions\RowAction;
class DiyAddButtonAction extends RowAction
{

    public $name = '';
    public function __construct()
    {
        $this->name  = '<div class="btn-group pull-right grid-create-btn" style="margin-right: 10px;float: right">
            <a href="sites-blog-categories/create?site_id='.request('site_id').'" class="btn btn-sm btn-success" title="新增">
                <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;新增</span>
            </a>
        </div>';
    }

    /**
     * @return  string
     */
    public function href()
    {

        return url('');
    }
}
