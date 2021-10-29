<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveSearchIntentionFromUrlsToKeywords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('urls', function (Blueprint $table) {
            $table->dropColumn('search_intention');
        });
        Schema::table('keywords', function (Blueprint $table) {
            $table->string('search_intention');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('urls', function (Blueprint $table) {
            $table->string('search_intention');
        });
        Schema::table('keywords', function (Blueprint $table) {
            $table->dropColumn('search_intention');
        });
    }
}
