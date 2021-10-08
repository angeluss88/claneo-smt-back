<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name' => 'admin',
            'description' => 'Admin',
        ]);
        DB::table('roles')->insert([
            'name' => 'SEO',
            'description' => 'SEO Manager',
        ]);
        DB::table('roles')->insert([
            'name' => 'Researcher',
            'description' => 'Researcher',
        ]);
        DB::table('roles')->insert([
            'name' => 'Client',
            'description' => 'Client',
        ]);
    }
}
