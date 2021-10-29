<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->integer('project_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('urls', function (Blueprint $table) {
            $table->integer('import_id')->unsigned()->nullable();
        });
        Schema::table('keywords', function (Blueprint $table) {
            $table->integer('import_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imports');
        Schema::table('urls', function (Blueprint $table) {
            $table->dropColumn('import_id');
        });
        Schema::table('keywords', function (Blueprint $table) {
            $table->dropColumn('import_id');
        });
    }
}
