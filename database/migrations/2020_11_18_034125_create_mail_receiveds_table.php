<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailReceivedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_receiveds', function (Blueprint $table) { //联系人回复的邮件管理
            $table->increments('id');
            $table->string('sender_email', 128)->default('')->comment("接收邮件的发件人地址");
            $table->string('receiver_email', 128)->default('')->comment("接收邮件的收件人地址");
            $table->string('title', 64)->default('')->comment("接收邮件标题");
            $table->text('content')->comment("接收邮件内容");
            $table->dateTime('receive_time')->comment('接收邮件时间');
            $table->tinyInteger('receive_status')->unsigned()->default(1)->comment("联系人合作意向状态，1:待处理，2：已处理");
            $table->tinyInteger('reply_status')->unsigned()->default(1)->comment("邮件回复状态，1:待回复，2：已回复");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_receiveds');
    }
}
