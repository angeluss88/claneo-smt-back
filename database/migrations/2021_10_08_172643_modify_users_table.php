<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email', 100)->change();

            $table->string('name', 100)->default('')->change();
            $table->renameColumn('name', 'first_name');

            $table->string('last_name', 100)->default('')->after('name');

            $table->tinyInteger('is_admin')->default(0)->after('password');
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
            $table->string('email', 255)->change();

            $table->string('first_name', 255)->default('')->change();
            $table->renameColumn('first_name', 'name');

            $table->dropColumn('last_name');

            $table->dropColumn('is_admin');
        });
    }
}
