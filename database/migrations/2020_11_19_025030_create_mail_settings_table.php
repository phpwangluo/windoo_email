<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMailSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_settings', function (Blueprint $table) {

            $table->increments('id');
            $table->string('driver', 50)->default('')->comment("邮箱驱动");
            $table->string('support_name', 100)->default('')->comment("邮箱运营商");
            $table->string('host', 100)->default('')->comment("邮箱所在主机");
            $table->integer('port')->default(0)->comment('发件箱端口号');
            $table->string('encryption',10)->default('')->comment('加密类型');
            $table->string('sendmail',200)->default('')->comment('是在设置driver为sendmail时使用，用于指定sendmail命令路径');
            $table->tinyInteger('pretend')->default(0)->comment('用于配置是否将邮件发送记录到日志中，默认为false则发送邮件不记录日志，如果为true的话只记录日志不发送邮件，这一配置在本地开发中调试时很有用');
            $table->tinyInteger('type')->default(0)->comment('发送人对应的邮箱运营商类型:0：个人，1：企业');
            //拉取邮箱内容的配置
            $table->string('getmail_protocol', 50)->default('')->comment("拉取邮件的邮箱驱动");
            $table->string('getmail_host', 100)->default('')->comment("拉取邮件的邮箱所在主机");
            $table->integer('getmail_port')->default(0)->comment('拉取邮件的端口号');
            $table->string('getmail_encryption',10)->default('')->comment('拉取邮件的加密类型');
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
        Schema::dropIfExists('mail_settings');
    }
}
