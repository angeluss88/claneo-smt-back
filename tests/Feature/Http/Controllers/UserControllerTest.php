<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_index()
    {
        $user = User::whereEmail('admin@loc')->first();
        $response = $this->actingAs($user)->get('/api/users');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('users.data')->has('users.total');
            })
            ->assertStatus(200);
        $this->assertEquals(User::count(), $response->json()['users']['total']);
    }

    public function test_index_non_auth()
    {
        $response = $this->get('/api/users');

        $response->assertStatus(401);
    }

    public function test_show()
    {
        User::whereEmail('test_user_delete_me@loc')->delete();

        $auth = User::whereEmail('admin@loc')->first();

        $user = User::create([
            'first_name' => 'test_user_for_delete',
            'last_name' => 'test_user_l',
            'email' => 'test_user_delete_me@loc',
            'password' => '12345',
        ]);

        $response = $this->actingAs($auth)->get('/api/users/' . $user->id);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('user')->missing('message');
            })
            ->assertStatus(200);

        $user->delete();
    }


    public function test_show_non_auth()
    {
        $response = $this->get('/api/users/' . 1);
        $response->assertStatus(401);
    }

    public function test_show_not_found()
    {
        User::whereEmail('test_user_delete_me@loc')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $id = DB::table('users')->latest('id')->first()->id;

        $response = $this->actingAs($user)->get('/api/users/' . ++$id);

        $response->assertStatus(404);
    }

    public function test_update()
    {
        User::whereEmail('test_user_delete_me@loc')->delete();
        User::whereEmail('test_user2_delete_me@loc')->delete();

        $auth = User::whereEmail('admin@loc')->first();

        $user = User::create([
            'first_name' => 'test_user_for_delete',
            'last_name' => 'test_user_l',
            'email' => 'test_user_delete_me@loc',
            'password' => '12345',
        ]);

        $response = $this->actingAs($auth)->put('/api/users/' . $user->id, [
            'email' => 'test_user2_delete_me@loc',
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('user')->missing('message');
            })
            ->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'test_user2_delete_me@loc',
        ]);

        User::whereEmail('test_user2_delete_me@loc')->delete();
    }

    public function test_update_non_auth()
    {
        User::whereEmail('test_user_delete_me@loc')->delete();
        User::whereEmail('test_user2_delete_me@loc')->delete();

        $user = User::create([
            'first_name' => 'test_user_for_delete',
            'last_name' => 'test_user_l',
            'email' => 'test_user_delete_me@loc',
            'password' => '12345',
        ]);

        $response = $this->put('/api/users/' . $user->id, [
            'email' => 'test_user2_delete_me@loc',
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'email' => 'test_user2_delete_me@loc',
        ]);
    }

    public function test_update_not_found()
    {
        User::whereEmail('test_user_delete_me@loc')->delete();
        $user = User::whereEmail('admin@loc')->first();
        $id = DB::table('users')->latest('id')->first()->id;

        $response = $this->actingAs($user)->put('/api/users/' . ++$id, [
            'email' => 'test_user_delete_me@loc',
        ]);
        $response->assertStatus(404);

        User::whereEmail('test_user_delete_me@loc')->delete();
    }

    public function test_destroy()
    {
        User::whereEmail('test_user_delete_me@loc')->delete();

        $auth = User::whereEmail('admin@loc')->first();

        $user = User::create([
            'first_name' => 'test_user_for_delete',
            'last_name' => 'test_user_l',
            'email' => 'test_user_delete_me@loc',
            'password' => '12345',
        ]);

        $response = $this->actingAs($auth)->delete('/api/users/' . $user->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'email' => 'test_user_delete_me@loc',
        ]);
    }


    public function test_destroy_non_auth()
    {
        User::whereEmail('test_user_delete_me@loc')->delete();

        $user = User::create([
            'first_name' => 'test_user_for_delete',
            'last_name' => 'test_user_l',
            'email' => 'test_user_delete_me@loc',
            'password' => '12345',
        ]);

        $response = $this->delete('/api/users/' . $user->id);

        $response->assertStatus(401);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'test_user_delete_me@loc',
        ]);
        $user->delete();
    }

    public function test_destroy_not_found()
    {
        $user = User::whereEmail('admin@loc')->first();
        $id = DB::table('users')->latest('id')->first()->id;

        $response = $this->actingAs($user)->delete('/api/users/' . ++$id);
        $response->assertStatus(404);
    }

}
