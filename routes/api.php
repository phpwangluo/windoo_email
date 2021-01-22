<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/countrylist', [App\Http\Controllers\Api\CountryController::class, 'countryList']);

Route::get('/tradelist', [App\Http\Controllers\Api\TradeController::class, 'tradeList']);
Route::post('/tradedelete', [App\Http\Controllers\Api\TradeController::class, 'deleteTrade']);

Route::get('/templatelist', [App\Http\Controllers\Api\TemplateController::class, 'templateList']);
Route::post('/templatedelete', [App\Http\Controllers\Api\TemplateController::class, 'deleteTemplate']);

Route::get('/contactlist', [App\Http\Controllers\Api\ContactController::class, 'contactList']);
Route::post('/contactstatus', [App\Http\Controllers\Api\ContactController::class, 'changeStatus']);

Route::get('/samemailsetiinglists', [App\Http\Controllers\Api\MailsettingController::class, 'samesettingList']);

Route::post('/senderdelete', [App\Http\Controllers\Api\SenderController::class, 'deleteSender']);


Route::post('/businessstatus', [App\Http\Controllers\Api\MailReceivedController::class, 'changeBusinessStatus']);


Route::post('/mailforsendstatus', [App\Http\Controllers\Api\MailForSendController::class, 'changeMailForSendStatus']);

Route::match(['get', 'post'],'/editorUpload', [App\Http\Controllers\Api\UploadController::class, 'editorUpload']);
Route::post('/uploadImg', [App\Http\Controllers\Api\UploadController::class, 'uploadImages']);
Route::post('/uploadFiles', [App\Http\Controllers\Api\UploadController::class, 'uploadFiles']);

Route::get('/blogs/categorylist', [\App\Http\Controllers\Api\Blogs\CategoriesController::class, 'categoryList']);
