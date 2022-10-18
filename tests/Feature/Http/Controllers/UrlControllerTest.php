<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\URL;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UrlControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_index()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $response = $this->actingAs($user)->get('/api/urls');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('kw_number')->has('url_number')->has('sv_sum')->has('urls.data')->has('urls.total');
            })
            ->assertStatus(200);
        $this->assertEquals(URL::count(), $response->json()['urls']['total']);
    }

    public function test_index_non_auth()
    {
        $response = $this->get('/api/urls');

        $response->assertStatus(401);
    }

    public function test_store()
    {
        URL::whereUrl('test_url_for_delete')->delete();

        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $response = $this->actingAs($user)->post('/api/urls', [
            'url' => 'test_url_for_delete',
            'status' => 'new',
            'main_category' => 'main_category',
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('url');
            })
            ->assertStatus(201);

        $this->assertDatabaseHas('urls', [
            'url' => 'test_url_for_delete'
        ]);
    }
    public function test_store_no_url_param()
    {
        URL::whereUrl('test_url_for_delete')->delete();

        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $response = $this->actingAs($user)->post('/api/urls', [
            'url1' => 'test_url_for_delete',
            'status' => 'new',
            'main_category' => 'main_category',
        ]);

        $response->assertStatus(500);

        $this->assertDatabaseMissing('urls', [
            'url' => 'test_url_for_delete'
        ]);
    }

    public function test_store_exists_url_param()
    {
        URL::whereUrl('test_url_for_delete')->delete();

        if($url = URL::first()->url) {
            $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
            $response = $this->actingAs($user)->post('/api/urls', [
                'url' => $url,
                'status' => 'new',
                'main_category' => 'main_category',
            ]);

            $response->assertStatus(500);
        }
    }

    public function test_store_non_auth()
    {
        URL::whereUrl('test_url_for_delete')->delete();

        $response = $this->post('/api/urls', [
            'url1' => 'test_url_for_delete'
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('urls', [
            'url' => 'test_url_for_delete'
        ]);
    }

    public function test_show()
    {
        URL::whereUrl('test_url_for_delete')->delete();

        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $url = URL::create([
            'url' => 'test_url_for_delete',
            'status' => 'new',
            'main_category' => 'main_category',
        ]);

        $response = $this->actingAs($user)->get('/api/urls/' . $url->id);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('url')->missing('message');
            })
            ->assertStatus(200);
    }


    public function test_show_non_auth()
    {
        $response = $this->get('/api/urls/' . 1);
        $response->assertStatus(401);
    }

    public function test_show_not_found()
    {
        URL::whereUrl('test_url_for_delete')->delete();

        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $last = DB::table('urls')->latest('id')->first();
        $id =  $last ? $last->id : 0;

        $response = $this->actingAs($user)->get('/api/urls/' . ++$id);

        $response->assertStatus(404);
    }

    public function test_update()
    {
        URL::whereUrl('test_url_for_delete')->delete();

        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $url = URL::create([
            'url' => 'test_url_for_delete',
            'status' => 'new',
            'main_category' => 'main_category',
        ]);

        $response = $this->actingAs($user)->put('/api/urls/' . $url->id, [
            'status' => '200',
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('url')->missing('message');
            })
            ->assertStatus(200);

        $this->assertDatabaseHas('urls', [
            'id' => $url->id,
            'url' => 'test_url_for_delete',
            'status' => '200',
        ]);

        URL::whereUrl('test_url_for_delete')->delete();
    }

    public function test_update_non_auth()
    {
        URL::whereUrl('test_url_for_delete')->delete();

        $url = URL::create([
            'url' => 'test_url_for_delete',
            'status' => 'new',
            'main_category' => 'main_category',
        ]);

        $response = $this->put('/api/urls/' . $url->id, [
            'url' => 'updated_test_url_for_delete',
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('urls', [
            'id' => $url->id,
            'url' => 'updated_test_url_for_delete',
        ]);
    }

    public function test_update_not_found()
    {
        URL::whereUrl('test_url_for_delete')->delete();
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $last = DB::table('urls')->latest('id')->first();
        $id =  $last ? $last->id : 0;

        $response = $this->actingAs($user)->put('/api/urls/' . ++$id, [
            'url' => 'you_never_see_me',
        ]);
        $response->assertStatus(404);

        URL::whereUrl('updated_test_url_for_delete')->delete();
    }

    public function test_destroy()
    {
        URL::whereUrl('test_url_for_delete')->delete();

        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $url = URL::create([
            'url' => 'delete_me',
            'status' => 'new',
            'main_category' => 'main_category',
        ]);

        $response = $this->actingAs($user)->delete('/api/urls/' . $url->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('urls', [
            'id' => $url->id,
            'url' => 'test_url_for_delete',
        ]);
    }


    public function test_destroy_non_auth()
    {
        URL::whereUrl('test_url_for_delete')->delete();

        $url = URL::create([
            'url' => 'test_url_for_delete',
            'status' => 'new',
            'main_category' => 'main_category',
        ]);

        $response = $this->delete('/api/urls/' . $url->id);

        $response->assertStatus(401);

        $this->assertDatabaseHas('urls', [
            'id' => $url->id,
            'url' => 'test_url_for_delete',
        ]);
        URL::whereUrl('test_url_for_delete')->delete();
    }

    public function test_destroy_not_found()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $last = DB::table('urls')->latest('id')->first();
        $id =  $last ? $last->id : 0;

        $response = $this->actingAs($user)->delete('/api/urls/' . ++$id);
        $response->assertStatus(404);
    }

    public function test_urlAggregation()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $response = $this->actingAs($user)->get('/api/urls_aggregation');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json
                    ->has('data')
                    ->has('data.aggrConvRate')
                    ->has('data.aggrRevenue')
                    ->has('data.aggrOrderValue')
                    ->has('data.aggrBounceRate')
                    ->has('data.aggrPosition')
                    ->has('data.aggrClicks')
                    ->has('data.aggrImpressions')
                    ->has('data.aggrCtr')
                    ->has('data.aggrSearchVolume');
            })
            ->assertStatus(200);
    }

    public function test_urlAggregation_non_auth()
    {
        $response = $this->get('/api/urls_aggregation');

        $response->assertStatus(401);
    }

}
