<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_roles_exist()
    {
        $this->assertDatabaseHas('roles', [
            'name' => 'admin',
        ]);
        $this->assertDatabaseHas('roles', [
            'name' => 'SEO',
        ]);
        $this->assertDatabaseHas('roles', [
            'name' => 'Researcher',
        ]);
        $this->assertDatabaseHas('roles', [
            'name' => 'Client',
        ]);
    }

    public function test_if_admin_exists()
    {
        $this->assertDatabaseHas('users', [
            'email' => 'admin@loc'
        ]);
    }
}
