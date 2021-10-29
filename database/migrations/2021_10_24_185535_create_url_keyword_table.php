<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrlKeywordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('url_keyword', function (Blueprint $table) {
            $table->id();
            $table->integer('url_id')->unsigned();
            $table->integer('keyword_id')->unsigned();
            $table->integer('current_ranking_position')->default(0);
            $table->string('clicks')->nullable();
            $table->string('impressions')->nullable();
            $table->string('ctr')->nullable();
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
        Schema::dropIfExists('url_keyword');
    }
}
