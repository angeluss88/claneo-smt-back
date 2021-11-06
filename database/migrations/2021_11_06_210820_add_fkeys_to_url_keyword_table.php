<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkeysToUrlKeywordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('url_keyword', function (Blueprint $table) {
            $table->bigInteger('url_id', false, true)->change();
            $table->bigInteger('keyword_id', false, true)->change();
            $table->index(['url_id', 'keyword_id']);
        });

        Schema::table('url_keyword', function (Blueprint $table) {
            $table->foreign('url_id')->references("id")->on("urls")->onDelete("cascade");
            $table->foreign('keyword_id')->references("id")->on("keywords")->onDelete("cascade");
        }) ;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       //
    }
}
