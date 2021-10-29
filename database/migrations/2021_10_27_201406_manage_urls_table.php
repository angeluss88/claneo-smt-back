<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ManageUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('urls', function (Blueprint $table) {
            $table->string('main_category');
            $table->integer('main_category_id')->nullable()->change();
            $table->string('sub_category')->nullable();
            $table->string('sub_category2')->nullable();
            $table->string('sub_category3')->nullable();
            $table->string('sub_category4')->nullable();
            $table->string('sub_category5')->nullable();
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
            $table->dropColumn('main_category');
            $table->integer('main_category_id')->nullable(false)->change();
            $table->dropColumn('sub_category');
            $table->dropColumn('sub_category2');
            $table->dropColumn('sub_category3');
            $table->dropColumn('sub_category4');
            $table->dropColumn('sub_category5');
        });
    }
}
