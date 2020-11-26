<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) { //国家管理
            $table->increments('id');
            $table->string('country_name', 16)->default('')->comment("国家名称");
            $table->string('country_code', 10)->default('')->unique('uk_country_code')->comment("国家编码");
            $table->tinyInteger('send_start_hour')->unsigned()->default(9)->comment("默认发送邮件开始时间点");
            $table->tinyInteger('send_end_hour')->unsigned()->default(17)->comment("默认发送邮件结束时间点");
            $table->tinyInteger('status')->unsigned()->default(1)->comment("国家状态");
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
        Schema::dropIfExists('countries');
    }
}
