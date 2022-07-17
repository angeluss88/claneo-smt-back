<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_show()
    {
        $user = User::whereEmail('admin@loc')->first();

        $response = $this->actingAs($user)->get('/api/account/');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('user')->missing('message');
            })
            ->assertStatus(200);
    }

    public function test_show_non_auth()
    {
        $response = $this->get('/api/account/');

        $response->assertStatus(401);
    }

    public function test_edit()
    {
        $user = User::whereEmail('admin@loc')->first();

        $response = $this->actingAs($user)->post('/api/account/edit', [
            'first_name' => 'admin_test_updated',
            'last_name' => 'admin_test_updated'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'admin_test_updated',
            'last_name' => 'admin_test_updated'
        ]);

        \DB::table('users')->where('id', $user->id)->update([
            'first_name' => 'admin_test',
            'last_name' => 'admin_test'
        ]);
    }

    public function test_edit_non_auth()
    {
        $response = $this->post('/api/account/edit');

        $response->assertStatus(401);
    }
}
