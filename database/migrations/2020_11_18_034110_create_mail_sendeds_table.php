<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMailSendedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_sendeds', function (Blueprint $table) {//发件人发件箱已发送的邮件
            $table->increments('id');
            $table->string('sender_email', 128)->default('')->comment("发送邮件的发件人地址");
            $table->string('receiver_email', 128)->default('')->comment("发送邮件的收件人地址");
            $table->string('title', 64)->default('')->comment("发送邮件标题");
            $table->text('content')->comment("发送邮件内容");
            //$table->integer('template_id')->unsigned()->default(0)->comment("要给联系人发送的邮件模板");
            $table->tinyInteger('send_type')->unsigned()->default(1)->comment("发送邮件类型，1:自动，2：手动");
            $table->dateTime('send_time')->comment('发件人发送邮件时间');
            $table->tinyInteger('send_status')->unsigned()->default(1)->comment("邮件发送状态，1:待发送?，2：已发送，3：取消发送 ，4：发送失败");
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
        Schema::dropIfExists('mail_sendeds');
    }
}
