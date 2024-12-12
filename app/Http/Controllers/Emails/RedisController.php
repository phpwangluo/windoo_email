<?php

namespace App\Http\Controllers\Emails;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redis;

class RedisController extends Controller
{
    public function test_redis(Request $request){
        // 使用
        dd(Redis::info());
    }
}
