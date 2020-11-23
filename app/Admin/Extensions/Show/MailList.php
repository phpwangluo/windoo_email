<?php


namespace App\Admin\Extensions\Show;

use Encore\Admin\Show\AbstractField;

class MailList extends AbstractField
{
    public function render($model = [])
    {
        // 返回自定义回复详情页面
        return view('admin/home');
    }
}
