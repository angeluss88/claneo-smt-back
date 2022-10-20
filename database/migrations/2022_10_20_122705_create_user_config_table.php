<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_config', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id', false, true)->nullable(false);
            $table->string('table_id')->nullable(false);
            $table->string('column')->nullable(false);
            $table->integer('position')->nullable(false)->default(0);
        });
        Schema::table('user_config', function (Blueprint $table) {
            $table->foreign('user_id')->references("id")->on("users")->onDelete("cascade");
        }) ;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_config');
    }
}
