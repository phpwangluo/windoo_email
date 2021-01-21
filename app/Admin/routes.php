<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');

    /**GP邮件管理系统 */
    $router->resource('countries', CountryController::class); //国家管理路由

    $router->resource('trades', TradeController::class); //行业管理路由

    $router->resource('templates', TemplateController::class); //模板管理路由

    $router->resource('senders', SenderController::class); //发件人/发件箱管理

    $router->resource('contacts', ContactController::class); //联系人管理

    $router->resource('mail-settings', MailSettingController::class); //邮箱配置管理

    $router->resource('mail-receiveds', MailReceivedController::class); //回复管理

    $router->resource('mail-details', MailDetailController::class);

    $router->resource('business-sources', BusinessSourceController::class);//合作资源管理

    $router->resource('mail-for-sends', MailForSendController::class);


    /** 站群内容管理系统 */

    $router->resource('sites-blog-sites', Sites\BlogSitesController::class); //站点配置
    $router->resource('sites-blog-articles', Sites\BlogArticlesController::class); //文章管理

});
