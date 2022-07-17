<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_index()
    {
        $user = User::whereEmail('admin@loc')->first();
        $response = $this->actingAs($user)->get('/api/clients');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('clients.data')->has('clients.total');
            })
            ->assertStatus(200);
        $this->assertEquals(Client::count(), $response->json()['clients']['total']);
    }

    public function test_index_non_auth()
    {
        $response = $this->get('/api/clients');

        $response->assertStatus(401);
    }

    public function test_store()
    {
        Client::whereName('test_client_for_delete')->delete();

        $user = User::whereEmail('admin@loc')->first();
        $response = $this->actingAs($user)->post('/api/clients', [
            'name' => 'test_client_for_delete'
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('client');
            })
            ->assertStatus(201);

        $this->assertDatabaseHas('clients', [
            'name' => 'test_client_for_delete'
        ]);
    }
    public function test_store_no_name_param()
    {
        Client::whereName('test_client_for_delete')->delete();

        $user = User::whereEmail('admin@loc')->first();
        $response = $this->actingAs($user)->post('/api/clients', [
            'name1' => 'test_client_for_delete'
        ]);

        $response->assertStatus(500);

        $this->assertDatabaseMissing('clients', [
            'name' => 'test_client_for_delete'
        ]);
    }

    public function test_store_exists_name_param()
    {
        Client::whereName('test_client_for_delete')->delete();

        if($name = Client::first()->name) {
            $user = User::whereEmail('admin@loc')->first();
            $response = $this->actingAs($user)->post('/api/clients', [
                'name' => $name,
            ]);

            $response->assertStatus(500);
        }
    }

    public function test_store_non_auth()
    {
        Client::whereName('test_client_for_delete')->delete();

        $response = $this->post('/api/clients', [
            'name1' => 'test_client_for_delete'
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('clients', [
            'name' => 'test_client_for_delete'
        ]);
    }

    public function test_show()
    {
        Client::whereName('test_client_for_delete')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $client = Client::create([
            'name' => 'test_client_for_delete',
        ]);

        $response = $this->actingAs($user)->get('/api/clients/' . $client->id);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('client')->missing('message');
            })
            ->assertStatus(200);
    }


    public function test_show_non_auth()
    {
        $response = $this->get('/api/clients/' . 1);
        $response->assertStatus(401);
    }

    public function test_show_not_found()
    {
        Client::whereName('test_client_for_delete')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $last = DB::table('clients')->latest('id')->first();
        $id =  $last ? $last->id : 0;

        $response = $this->actingAs($user)->get('/api/clients/' . ++$id);

        $response->assertStatus(404);
    }

    public function test_update()
    {
        Client::whereName('test_client_for_delete')->delete();
        Client::whereName('updated_test_client_for_delete')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $client = Client::create([
            'name' => 'test_client_for_delete',
        ]);

        $response = $this->actingAs($user)->put('/api/clients/' . $client->id, [
            'name' => 'updated_test_client_for_delete',
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('client')->missing('message');
            })
            ->assertStatus(200);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'updated_test_client_for_delete',
        ]);

        Client::whereName('updated_test_client_for_delete')->delete();
    }

    public function test_update_non_auth()
    {
        Client::whereName('test_client_for_delete')->delete();

        $client = Client::create([
            'name' => 'test_client_for_delete',
        ]);

        $response = $this->put('/api/clients/' . $client->id, [
            'name' => 'updated_test_client_for_delete',
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
            'name' => 'updated_test_client_for_delete',
        ]);
    }

    public function test_update_not_found()
    {
        Client::whereName('test_client_for_delete')->delete();
        $user = User::whereEmail('admin@loc')->first();
        $last = DB::table('clients')->latest('id')->first();
        $id =  $last ? $last->id : 0;

        $response = $this->actingAs($user)->put('/api/clients/' . ++$id, [
            'name' => 'you_never_see_me',
        ]);
        $response->assertStatus(404);

        Client::whereName('updated_test_client_for_delete')->delete();
    }

    public function test_destroy()
    {
        Client::whereName('test_client_for_delete')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $client = Client::create([
            'name' => 'delete_me',
        ]);

        $response = $this->actingAs($user)->delete('/api/clients/' . $client->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
            'name' => 'test_client_for_delete',
        ]);
    }


    public function test_destroy_non_auth()
    {
        Client::whereName('test_client_for_delete')->delete();

        $client = Client::create([
            'name' => 'test_client_for_delete',
        ]);

        $response = $this->delete('/api/clients/' . $client->id);

        $response->assertStatus(401);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'test_client_for_delete',
        ]);
        Client::whereName('test_client_for_delete')->delete();
    }

    public function test_destroy_not_found()
    {
        $user = User::whereEmail('admin@loc')->first();
        $last = DB::table('clients')->latest('id')->first();
        $id =  $last ? $last->id : 0;

        $response = $this->actingAs($user)->delete('/api/clients/' . ++$id);
        $response->assertStatus(404);
    }

}
