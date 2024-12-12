<?php

namespace App\Exceptions;

use App\Exceptions\Diy\EmailException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Exception;
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    // Handler的render函数
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request,\Throwable $e)
    {
        // 如果config配置debug为true ==>debug模式的话让laravel自行处理
        if(config('app.debug')){
            return parent::render($request, $e);
        }
        return $this->handle($request, $e);
    }

    // 新添加的handle函数
    public function handle($request, Exception $e){
        if($e instanceof EmailException) {
            $result = [
                "msg"    => $e->getMessage(),
                "data"   => $request->get('data'),
                "status" => $e->getCode()
            ];
            return response()->json($result);
        }
        return parent::render($request, $e);
    }
}
