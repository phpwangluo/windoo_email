<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    public function index(Content $content)
    {
        $email = DB::table('senders')
            ->select(DB::raw('sum(send_count) as s, sum(receive_count) as r'))
            ->get()->toArray();
        return $content
            ->header('<h1 style="text-align:center">欢迎使用GP邮件管理系统</h1>')
            ->body(new Box('发件箱邮件统计', view('admin.home',compact('email'))));
        /*return $content
            ->title('Dashboard')
            ->description('Description...')
            ->row(Dashboard::title())
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });*/
    }
}
