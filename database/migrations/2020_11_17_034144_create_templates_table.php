<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {//模板管理
            $table->increments('id');
            $table->string('template_name', 32)->default('')->comment("模板名称");
            $table->string('email_title', 64)->default('')->comment("邮件标题");
            $table->text('email_content')->comment("邮件内容");
            $table->string('template_sign', 512)->default('')->comment("邮件内容");
            $table->integer('country_id')->unsigned()->default(0)->comment("国家ID");
            $table->integer('trade_id')->unsigned()->default(0)->comment("行业ID");
            $table->tinyInteger('status')->unsigned()->default(1)->comment("模板状态,0:删除");
            //$table->timestamps();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('templates');
    }
}
