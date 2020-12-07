<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */
use Encore\Admin\Show;
use App\Admin\Extensions\Show\UnSerialize;
use App\Admin\Extensions\Show\MailList;
use App\Admin\Extensions\Tools\Simditor;
use Encore\Admin\Form;
use App\Admin\Extensions\Show\MailContentDiyShow;
use Encore\Admin\Grid;
//Encore\Admin\Form::forget(['map', 'editor']);
Form::extend('seditor', Simditor::class);
Show::extend('unserialize', UnSerialize::class);
show::extend('replylist',MailList::class);
show::extend('mailforsendcontent',MailContentDiyShow::class);

Grid::init(function (Grid $grid) {
    $grid->actions(function (Grid\Displayers\Actions $actions) {
        $actions->disableView();
        $actions->disableEdit();
        $actions->disableDelete();
    });
});
