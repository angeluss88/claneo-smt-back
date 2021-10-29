<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('urls', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('status');
            $table->string('page_type');
            $table->string('search_intention');
            $table->integer('main_category_id')->unsigned();
            $table->integer('sub_category_id')->unsigned()->nullable(true);
            $table->integer('sub_category2_id')->unsigned()->nullable(true);
            $table->integer('sub_category3_id')->unsigned()->nullable(true);
            $table->integer('sub_category4_id')->unsigned()->nullable(true);
            $table->integer('sub_category5_id')->unsigned()->nullable(true);
            $table->string('ecom_conversion_rate')->nullable(true);
            $table->string('revenue')->nullable(true);
            $table->string('avg_order_value')->nullable(true);
            $table->string('bounce_rate')->nullable(true);
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
        Schema::dropIfExists('urls');
    }
}
