<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//测试发送邮件

Route::get('/sendmail', [App\Http\Controllers\Emails\SendmailController::class, 'test']);

//测试接收邮件
Route::get('/getmail', [App\Http\Controllers\Emails\GetmailController::class, 'getEmail']);

Route::get('/createmail', [App\Http\Controllers\Emails\CreatemailController::class, 'test']);

Route::get('/export_sender_template', [App\Http\Controllers\Emails\ExportSenderTemplateController::class, 'export']);

Route::get('/export_contact_template', [App\Http\Controllers\Emails\ExportContactTemplateController::class, 'export']);



