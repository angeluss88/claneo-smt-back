<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExpandDataPerEachDayWorkflow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('urls', function (Blueprint $table) {
            $table->dropColumn(['ecom_conversion_rate', 'revenue', 'avg_order_value', 'bounce_rate']);
        });
        Schema::create('url_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('url_id', false, true);
            $table->string('ecom_conversion_rate')->nullable(true);
            $table->string('revenue')->nullable(true);
            $table->string('avg_order_value')->nullable(true);
            $table->string('bounce_rate')->nullable(true);
            $table->date('date');
        });
        Schema::table('url_data', function (Blueprint $table) {
            $table->foreign('url_id')->references("id")->on("urls")->onDelete("cascade");
        }) ;

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('urls', function (Blueprint $table) {
            $table->string('ecom_conversion_rate')->nullable(true);
            $table->string('revenue')->nullable(true);
            $table->string('avg_order_value')->nullable(true);
            $table->string('bounce_rate')->nullable(true);
        });
        Schema::dropIfExists('url_data');
    }
}
