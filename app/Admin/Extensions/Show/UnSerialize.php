<?php


namespace App\Admin\Extensions\Show;

use Encore\Admin\Show\AbstractField;

class UnSerialize extends AbstractField
{
    public function render($arg = 'dsad')
    {
        $return = '
       <table>
            <thead>sdfsdf</thead>
            <tr><td>邮箱</td><td>1617049813@qq.com</td></tr>
            <tr><td>邮箱</td><td>1617049813@qq.com</td></tr>
            <tr><td>邮箱</td><td>1617049813@qq.com</td></tr>
</table>
        ';
        // 返回任意可被渲染的内容
        return view('admin/home');
    }
}
