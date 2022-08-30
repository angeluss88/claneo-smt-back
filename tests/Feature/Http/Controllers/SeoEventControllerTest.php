<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Project;
use App\Models\SeoEvent;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class SeoEventControllerTest extends TestCase
{
    const SEO_EVENT_TITLE = 'test_seo_event_for_delete';
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_index()
    {
        SeoEvent::whereTitle(self::SEO_EVENT_TITLE)->delete();
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $response = $this->actingAs($user)->get('/api/seo_events');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('seo_events.data')->has('seo_events.total');
            })
            ->assertStatus(200);
        $this->assertEquals(SeoEvent::count(), $response->json()['seo_events']['total']);
    }

    public function test_index_non_auth()
    {
        $response = $this->get('/api/seo_events');

        $response->assertStatus(401);
    }

    public function test_store()
    {
        SeoEvent::whereTitle(self::SEO_EVENT_TITLE)->delete();
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $response = $this->actingAs($user)->post('/api/seo_events', [
            'title' => self::SEO_EVENT_TITLE,
            'description' => 'test_seo_event_for_delete',
            'entity_type' => SeoEvent::PROJECT_TYPE,
            'entity_id' => 1,
            'date' => '2023-12-22'
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('seo_event');
            })
            ->assertStatus(201);

        $this->assertDatabaseHas('seo_events', [
            'title' => self::SEO_EVENT_TITLE,
        ]);

    }

    public function test_store_no_title_param()
    {
        SeoEvent::whereTitle(self::SEO_EVENT_TITLE)->delete();

        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $response = $this->actingAs($user)->post('/api/seo_events', [
            'name' => self::SEO_EVENT_TITLE
        ]);

        $response->assertStatus(500);

        $this->assertDatabaseMissing('seo_events', [
            'title' => self::SEO_EVENT_TITLE
        ]);
    }

    public function test_store_non_auth()
    {
        SeoEvent::whereTitle(self::SEO_EVENT_TITLE)->delete();

        $response = $this->post('/api/seo_events', [
            'title' => self::SEO_EVENT_TITLE
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('seo_events', [
            'title' => self::SEO_EVENT_TITLE
        ]);
    }

    public function test_show()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $seoEvent = SeoEvent::create([
            'title' => self::SEO_EVENT_TITLE,
            'description' => 'Please delete me',
            'date' => '2022-01-01',
            'entity_type' => Project::class,
            'entity_id' => 1,
        ]);

        $response = $this->actingAs($user)->get('/api/seo_events/' . $seoEvent->id);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('seo_event')->missing('message');
            })
            ->assertStatus(200);

        $seoEvent->delete();
    }

    public function test_show_non_auth()
    {
        $response = $this->get('/api/seo_events/' . 1);
        $response->assertStatus(401);
    }

    public function test_show_not_found()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $response = $this->actingAs($user)->get('/api/seo_events/0');

        $response->assertStatus(404);
    }

    public function test_update()
    {
        SeoEvent::whereTitle(self::SEO_EVENT_TITLE)->delete();
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $seoEvent = SeoEvent::create([
            'title' => self::SEO_EVENT_TITLE,
            'description' => 'Please delete me',
            'date' => '2022-01-01',
            'entity_type' => Project::class,
            'entity_id' => 1,
        ]);

        $response = $this->actingAs($user)->put('/api/seo_events/' . $seoEvent->id, [
            'title' => 'updated_' . self::SEO_EVENT_TITLE,
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('seo_event')->missing('message');
            })
            ->assertStatus(200);

        $this->assertDatabaseHas('seo_events', [
            'id' => $seoEvent->id,
            'title' => 'updated_' . self::SEO_EVENT_TITLE,
        ]);

        $seoEvent->delete();
    }

    public function test_update_non_auth()
    {
        $seoEvent = SeoEvent::create([
            'title' => self::SEO_EVENT_TITLE,
            'description' => 'Please delete me',
            'date' => '2022-01-01',
            'entity_type' => Project::class,
            'entity_id' => 1,
        ]);

        $response = $this->put('/api/seo_events/' . $seoEvent->id, [
            'title' => 'updated_' . self::SEO_EVENT_TITLE,
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('seo_events', [
            'id' => $seoEvent->id,
            'title' => 'updated_' . self::SEO_EVENT_TITLE,
        ]);
    }

    public function test_update_not_found()
    {
        SeoEvent::whereTitle(self::SEO_EVENT_TITLE)->delete();
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $response = $this->actingAs($user)->put('/api/seo_events/0', [
            'title' => 'you_never_see_me',
        ]);
        $response->assertStatus(404);
    }

    public function test_destroy()
    {
        SeoEvent::whereTitle(self::SEO_EVENT_TITLE)->delete();

        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $seoEvent = SeoEvent::create([
            'title' => self::SEO_EVENT_TITLE,
            'description' => 'Please delete me',
            'date' => '2022-01-01',
            'entity_type' => Project::class,
            'entity_id' => 1,
        ]);

        $response = $this->actingAs($user)->delete('/api/seo_events/' . $seoEvent->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('seo_events', [
            'id' => $seoEvent->id,
            'title' => self::SEO_EVENT_TITLE,
        ]);
    }

    public function test_destroy_non_auth()
    {
        SeoEvent::whereTitle(self::SEO_EVENT_TITLE)->delete();

        $response = $this->delete('/api/seo_events/0' );

        $response->assertStatus(401);
    }

    public function test_destroy_not_found()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $response = $this->actingAs($user)->delete('/api/seo_events/0');
        $response->assertStatus(404);
    }

}
