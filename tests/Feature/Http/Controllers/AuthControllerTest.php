<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public $token;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@loc',
            'password' => '12345'
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('user')->has('token')->missing('message');
            })
            ->assertStatus(200);

        $this->actingAs(User::whereEmail('admin@loc')->firstOrFail());
        auth()->user()->tokens()->delete(); // @TODO remove after configuring refreshDB
    }

    public function test_register_wrong_data()
    {
        $user = User::whereEmail('admin@loc')->first();

        $response = $this->actingAs($user)
            ->post('/api/register',[
                'email' => 'user@gmail.com'
            ]);

        $response->assertStatus(500);
    }

    public function test_register()
    {
        User::whereEmail('delete_this_if_you_see@loc')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $response = $this->actingAs($user)
            ->post('/api/register',[
                "email" => "delete_this_if_you_see@loc",
                "first_name" => "John",
                "last_name" => "Doe",
                "client" => "Client",
                "roles" => [
                    1,
                    2
                ]
            ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('user')->missing('message');
            })
            ->assertStatus(201);
    }

    public function test_register_wrong_role()
    {
        $user = User::whereEmail('client@loc')->first();

        $response = $this->actingAs($user)
            ->post('/api/register',[
                'email' => 'user@gmail.com'
            ]);
        $response->assertStatus(401);
    }

    public function test_register_Client()
    {
        User::whereEmail('delete_this_if_you_see@loc')->delete();

        $user = User::whereEmail('admin@loc')->first();
        $client = Client::firstOrFail();

        $response = $this->actingAs($user)
            ->post('/api/register',[
                "email" => "delete_this_if_you_see@loc",
                "first_name" => "John",
                "last_name" => "Doe",
                "client" => $client->name,
                "roles" => [
                    3
                ]
            ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('user')->missing('message');
            })
            ->assertStatus(201);
    }

    public function test_logout()
    {
        $user = User::whereEmail('admin@loc')->first();

        $response = $this->actingAs($user)->post('/api/logout');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('message');
            })
            ->assertStatus(200);
    }

    public function test_logout_unauthenticated()
    {
        $response = $this->post('/api/logout');

        $response->assertStatus(401);
    }

    public function test_forgotPassword()
    {
        User::whereEmail('delete_this_if_you_see@loc')->delete();

        $user = User::create([
            'email' => 'delete_this_if_you_see@loc',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'password' => Hash::make('password'),
        ]);
        $response = $this->post('/api/forgot-pwd', [
            'email' => $user->email,
            'prevent_send' => 1,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('password_resets', [
            'email' => $user->email,
        ]);

        DB::table('password_resets')->where('email', $user->email)->delete(); //  @TODO delete this after configuring refresh DB
    }

    public function test_forgetPassword_with_auth_user()
    {
        $user = User::firstOrFail();
        $response = $this->actingAs($user)->post('/api/forgot-pwd');

        $response->assertStatus(403);
    }

    public function test_changePassword()
    {
        User::whereEmail('delete_this_if_you_see@loc')->delete();

        $user = User::create([
            'email' => 'delete_this_if_you_see@loc',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'password' => Hash::make('password'),
        ]);

        $user->roles()->sync([1,2]);
        $user->save();

        $token = Str::random();
        $created_at = Carbon::now();

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => $created_at,
        ]);

        $response = $this->post('/api/change-pwd', [
            'email' => $user->email,
            'password' => '12345',
            'password_confirmation' => '12345',
            'token' => $token,
        ]);

        $this->assertDatabaseMissing('password_resets', [
            'email' => $user->email,
            'token' => $token,
        ]);

        $response->assertStatus(200);
        $this->actingAs($user)->assertAuthenticatedAs($user);
    }

    public function test_changePassword_for_client_with_wrong_data()
    {
        User::whereEmail('delete_this_if_you_see@loc')->delete();

        $user = User::create([
            'email' => 'delete_this_if_you_see@loc',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'password' => Hash::make('password'),
            'privacy_policy_flag' => 1,
        ]);

        $user->roles()->sync([4]);
        $user->save();

        $token = Str::random();
        $created_at = Carbon::now();

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => $created_at,
        ]);

        $response = $this->post('/api/change-pwd', [
            'email' => $user->email,
            'password' => '12345',
            'password_confirmation' => '12345',
            'token' => '123',
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('message');
            })
            ->assertStatus(422);

        DB::table('password_resets')->where('email', 'delete_this_if_you_see@loc')->delete();
        User::whereEmail('delete_this_if_you_see@loc')->delete();
    }

}
