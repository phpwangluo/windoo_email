<?php


namespace App\Admin\Extensions\Show;

use Encore\Admin\Show\AbstractField;

class UnSerialize extends AbstractField
{
    public function render($arg = '')
    {
        // 返回任意可被渲染的内容
        return unserialize($this->value);
    }
}
