<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('senders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email_address', 128)->default('')->comment("邮箱地址");
            $table->string('email_pass',32)->default('')->comment('邮箱密码');
            $table->string('email_sign',512)->default('')->comment('邮箱签名');
            $table->integer('send_count')->default(0)->unsigned()->comment('发送邮件次数');
            $table->integer('receive_count')->default(0)->unsigned()->comment('接收邮件次数');
            $table->integer('max_send_count')->default(1000)->unsigned()->comment('最大发送次数');
            $table->tinyInteger('email_status')->unsigned()->default(1)->comment("发件人状态，0:停用，1：使用中");
            $table->tinyInteger('status')->unsigned()->default(1)->comment("发件人状态，0:删除，1：正常");
            $table->string('remarks',512)->default('')->comment('备注');
            $table->integer('mail_setting_id')->default(1)->unsigned()->comment('关联邮箱运营商配置');
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
        Schema::dropIfExists('senders');
    }
}
