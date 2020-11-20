<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) { //联系人管理
            $table->increments('id');
            $table->string('email_address', 128)->default('')->comment("联系人邮箱地址");
            $table->integer('country_id')->default(0)->comment('联系人所属国家');
            $table->integer('trade_id')->unsigned()->default(0)->comment("联系人所属行业");
            $table->integer('template_id')->unsigned()->default(0)->comment("要给联系人发送的邮件模板");
            $table->string('customer_tag', 64)->default('')->comment("联系人标签");
            $table->tinyInteger('send_start_hour')->unsigned()->default(9)->comment("联系人发送邮件开始时间点");
            $table->tinyInteger('send_end_hour')->unsigned()->default(17)->comment("联系人发送邮件结束时间点");
            $table->integer('send_count')->default(0)->unsigned()->comment('给此联系人发送邮件次数');
            $table->tinyInteger('business_status')->unsigned()->default(1)->comment("联系人的合作状态，0：不合作 1:合作中，2：已合作");
            $table->tinyInteger('task_status')->unsigned()->default(1)->comment("联系人任务状态，0:停用，1：正常");
            $table->tinyInteger('status')->unsigned()->default(1)->comment("联系人状态，0:删除，1：正常");
            $table->string('remarks',64)->default('')->comment('备注');
            $table->timestamps();
        });

        Schema::create('mail_for_sends', function (Blueprint $table) { //联系人配置的发送邮件内容
            $table->increments('id');
            //$table->string('sender_email', 128)->default('')->comment("发送邮件的发件人地址");
            $table->string('receiver_email', 128)->default('')->comment("发送邮件的收件人地址");
            $table->string('title', 64)->default('')->comment("发送邮件标题");
            $table->text('content')->comment("发送邮件内容");
            $table->tinyInteger('send_type')->unsigned()->default(1)->comment("发送邮件类型，1:自动，2：手动");
            $table->tinyInteger('send_start_hour')->unsigned()->default(9)->comment("联系人发送邮件开始时间点");
            $table->tinyInteger('send_end_hour')->unsigned()->default(17)->comment("联系人发送邮件结束时间点");
            $table->tinyInteger('send_max_num')->unsigned()->default(5)->comment("允许给联系人发送邮件的最大次数，默认5次");
            $table->date('send_date')->default('1990-01-01')->comment("处理发送邮件的日期");
            $table->tinyInteger('send_status')->unsigned()->default(1)->comment("是否允许邮件发送，1:允许，2：停止");
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
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('mail_forsend');
    }
}
