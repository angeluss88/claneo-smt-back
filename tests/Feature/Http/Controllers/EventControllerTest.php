<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_index()
    {
        $user = User::whereEmail('admin@loc')->first();
        $response = $this->actingAs($user)->get('/api/events');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('events.data')->has('events.total');
            })
            ->assertStatus(200);
        $this->assertEquals(Event::count(), $response->json()['events']['total']);
    }

    public function test_index_non_auth()
    {
        $response = $this->get('/api/events');

        $response->assertStatus(401);
    }
}
