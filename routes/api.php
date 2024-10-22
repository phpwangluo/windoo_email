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
Route::get('/templatelist', [App\Http\Controllers\Api\TemplateController::class, 'templateList']);
Route::get('/contactlist', [App\Http\Controllers\Api\ContactController::class, 'contactList']);

Route::get('/samemailsetiinglists', [App\Http\Controllers\Api\MailsettingController::class, 'samesettingList']);

