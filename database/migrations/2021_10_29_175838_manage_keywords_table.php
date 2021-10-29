<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ManageKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('keywords', function (Blueprint $table) {
            $table->string('featured_snippet_keyword')->nullable()->change();
            $table->string('featured_snippet_owned')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('keywords', function (Blueprint $table) {
            $table->tinyInteger('featured_snippet_keyword')->nullable()->change();
            $table->tinyInteger('featured_snippet_owned')->nullable()->change();
        });
    }
}
