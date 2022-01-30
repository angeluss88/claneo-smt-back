<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpandGcsPerDayWorkflow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('url_keyword', function (Blueprint $table) {
            $table->dropColumn(['clicks', 'impressions', 'ctr']);
        });
        Schema::create('url_keyword_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('url_keyword_id', false, true);
            $table->string('position')->nullable(true);
            $table->string('clicks')->nullable(true);
            $table->string('impressions')->nullable(true);
            $table->string('ctr')->nullable(true);
            $table->date('date');
        });
        Schema::table('url_keyword_data', function (Blueprint $table) {
            $table->foreign('url_keyword_id')->references("id")->on("url_keyword")->onDelete("cascade");
        }) ;

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('url_keyword', function (Blueprint $table) {
            $table->string('clicks')->nullable(true);
            $table->string('impressions')->nullable(true);
            $table->string('ctr')->nullable(true);
        });
        Schema::dropIfExists('url_keyword_data');
    }
}
