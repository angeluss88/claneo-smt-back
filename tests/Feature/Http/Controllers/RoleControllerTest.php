<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_index()
    {
        $user = User::whereEmail('admin@loc')->first();

        $response = $this->actingAs($user)->get('/api/roles');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('roles')->missing('message');
            })
            ->assertStatus(200);
    }

    public function test_index_non_auth()
    {
        $response = $this->get('/api/roles');
        $response->assertStatus(401);
    }

    public function test_store_role()
    {
        Role::whereName('delete_me')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $response = $this->actingAs($user)->post('/api/roles', [
            'name' => 'delete_me',
            'description' => 'simple_test_role',
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('role')->missing('message');
            })
            ->assertStatus(201);

        $this->assertDatabaseHas('roles', [
            'name' => 'delete_me',
            'description' => 'simple_test_role'
        ]);
    }

    public function test_store_role_non_auth()
    {
        Role::whereName('delete_me')->delete();

        $response = $this->post('/api/roles', [
            'name' => 'delete_me',
            'description' => 'simple_test_role',
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('roles', [
            'name' => 'delete_me',
            'description' => 'simple_test_role'
        ]);
    }

    public function test_store_role_missing_description()
    {
        Role::whereName('delete_me')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $response = $this->actingAs($user)->post('/api/roles', [
            'name' => 'delete_me',
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('status')->has('message')->missing('role');
            })
            ->assertStatus(500);

        $this->assertDatabaseMissing('roles', [
            'name' => 'delete_me',
            'description' => 'simple_test_role'
        ]);
    }

    public function test_show()
    {
        Role::whereName('delete_me')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $role = Role::create([
            'name' => 'delete_me',
            'description' => 'simple_test_role',
        ]);

        $response = $this->actingAs($user)->get('/api/roles/' . $role->id);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('role')->missing('message');
            })
            ->assertStatus(200);
    }


    public function test_show_non_auth()
    {
        $response = $this->get('/api/roles/' . 1);

        $response->assertStatus(401);
    }

    public function test_show_not_found()
    {
        Role::whereName('delete_me')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $id = \DB::table('roles')->latest('id')->first()->id + 1;

        $response = $this->actingAs($user)->get('/api/roles/' . $id);

        $response->assertStatus(404);
    }

    public function test_update()
    {
        Role::whereName('delete_me')->delete();
        Role::whereName('updated_test_role_delete_it')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $role = Role::create([
            'name' => 'delete_me',
            'description' => 'simple_test_role',
        ]);

        $response = $this->actingAs($user)->put('/api/roles/' . $role->id, [
            'name' => 'updated_test_role_delete_it',
            'description' => 'updated, but still needs to be deleted'
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('role')->missing('message');
            })
            ->assertStatus(200);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'updated_test_role_delete_it',
            'description' => 'updated, but still needs to be deleted'
        ]);

        Role::whereName('updated_test_role_delete_it')->delete();
    }


    public function test_update_non_auth()
    {
        Role::whereName('delete_me')->delete();

        $role = Role::create([
            'name' => 'delete_me',
            'description' => 'simple_test_role',
        ]);

        $response = $this->put('/api/roles/' . $role->id, [
            'name' => 'updated_test_role_delete_it',
            'description' => 'updated, but still needs to be deleted'
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
            'name' => 'updated_test_role_delete_it',
            'description' => 'updated, but still needs to be deleted'
        ]);
    }

    public function test_update_not_found()
    {
        Role::whereName('delete_me')->delete();
        $user = User::whereEmail('admin@loc')->first();
        $id = \DB::table('roles')->latest('id')->first()->id + 1;

        $response = $this->actingAs($user)->put('/api/roles/' . $id, [
            'name' => 'you_never_see_me',
            'description' => 'you_never_see_me'
        ]);
        $response->assertStatus(404);

        Role::whereName('updated_test_role_delete_it')->delete();
    }

    public function test_destroy()
    {
        Role::whereName('delete_me')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $role = Role::create([
            'name' => 'delete_me',
            'description' => 'simple_test_role',
        ]);

        $response = $this->actingAs($user)->delete('/api/roles/' . $role->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
            'name' => 'delete_me',
            'description' => 'simple_test_role'
        ]);
    }


    public function test_destroy_non_auth()
    {
        Role::whereName('delete_me')->delete();

        $role = Role::create([
            'name' => 'delete_me',
            'description' => 'simple_test_role',
        ]);

        $response = $this->delete('/api/roles/' . $role->id);

        $response->assertStatus(401);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'delete_me',
            'description' => 'simple_test_role'
        ]);
        Role::whereName('delete_me')->delete();
    }

    public function test_destroy_not_found()
    {
        $user = User::whereEmail('admin@loc')->first();
        $id = \DB::table('roles')->latest('id')->first()->id + 1;

        $response = $this->actingAs($user)->delete('/api/roles/' . $id);
        $response->assertStatus(404);
    }

}
