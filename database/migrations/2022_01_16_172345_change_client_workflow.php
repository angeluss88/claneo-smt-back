<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeClientWorkflow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('client_id', false, true)->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('client_id')->references("id")->on("clients")->onDelete("cascade");
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->bigInteger('client_id', false, true)->nullable();
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('client_id')->references("id")->on("clients")->onDelete("cascade");
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['users_client_id_foreign']);
        });

        Schema::table('imports', function (Blueprint $table) {
            $table->dropColumn('client_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['projects_client_id_foreign']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('client_id');
            $table->integer('user_id')->unsigned()->nullable();
        });
    }
}
