<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_sources', function (Blueprint $table) { //合作资源管理
            $table->increments('id');
            $table->string('email_address', 128)->default('')->comment("联系人邮箱地址");
            $table->string('other_contact', 256)->default('')->comment("其他联系方式");
            $table->integer('country_id')->default(0)->comment('联系人所属国家');
            $table->integer('trade_id')->unsigned()->default(0)->comment("联系人所属行业");
            $table->string('customer_tag', 64)->default('')->comment("联系人标签");
            $table->integer('da')->unsigned()->default(0)->comment('DA值');
            $table->tinyInteger('score_level')->unsigned()->default(3)->comment('评级，1：低，2：中，3：高');
            $table->string('home_page', 256)->default('')->comment("网址");
            $table->tinyInteger('business_status')->unsigned()->default(1)->comment("联系人的合作状态，0：不合作 1:合作中，2：已合作");
            $table->string('remarks',64)->default('')->comment('备注');
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
        Schema::dropIfExists('business_sources');
    }
}
