<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Keyword;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class KeywordControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_index()
    {
        $user = User::whereEmail('admin@loc')->first();
        $response = $this->actingAs($user)->get('/api/keywords');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('keywords.data')->has('keywords.total');
            })
            ->assertStatus(200);
        $this->assertEquals(Keyword::count(), $response->json()['keywords']['total']);
    }

    public function test_index_non_auth()
    {
        $response = $this->get('/api/keywords');

        $response->assertStatus(401);
    }

    public function test_store()
    {
        Keyword::whereKeyword('test_keyword_for_delete')->delete();

        $user = User::whereEmail('admin@loc')->first();
        $response = $this->actingAs($user)->post('/api/keywords', [
            'keyword' => 'test_keyword_for_delete',
            "search_volume" => 1,
            "search_volume_clustered" => 1,
            "current_ranking_url" => "https://www.site.com/page",
            "featured_snippet_keyword" => "yes",
            "featured_snippet_owned" => "yes",
            "search_intention" => "transactional",
            "current_ranking_position" => "1",
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('keyword');
            })
            ->assertStatus(201);

        $this->assertDatabaseHas('keywords', [
            'keyword' => 'test_keyword_for_delete'
        ]);
    }
    public function test_store_no_keyword_param()
    {
        Keyword::whereKeyword('test_keyword_for_delete')->delete();

        $user = User::whereEmail('admin@loc')->first();
        $response = $this->actingAs($user)->post('/api/keywords', [
            'keyword1' => 'test_keyword_for_delete',
            "search_volume" => 1,
            "search_volume_clustered" => 1,
            "current_ranking_url" => "https://www.site.com/page",
            "featured_snippet_keyword" => "yes",
            "featured_snippet_owned" => "yes",
            "search_intention" => "transactional",
            "current_ranking_position" => "1",
        ]);

        $response->assertStatus(500);

        $this->assertDatabaseMissing('keywords', [
            'keyword' => 'test_keyword_for_delete'
        ]);
    }

    public function test_store_exists_keyword_param()
    {
        Keyword::whereKeyword('test_keyword_for_delete')->delete();

        if($keyword = Keyword::first()->keyword) {
            $user = User::whereEmail('admin@loc')->first();
            $response = $this->actingAs($user)->post('/api/keywords', [
                'keyword' => $keyword,
                "search_volume" => 1,
                "search_volume_clustered" => 1,
                "current_ranking_url" => "https://www.site.com/page",
                "featured_snippet_keyword" => "yes",
                "featured_snippet_owned" => "yes",
                "search_intention" => "transactional",
                "current_ranking_position" => "1",
            ]);

            $response->assertStatus(500);
        }
    }

    public function test_store_non_auth()
    {
        Keyword::whereKeyword('test_keyword_for_delete')->delete();

        $response = $this->post('/api/keywords', [
            'keyword' => 'test_keyword_for_delete',
            "search_volume" => 1,
            "search_volume_clustered" => 1,
            "current_ranking_url" => "https://www.site.com/page",
            "featured_snippet_keyword" => "yes",
            "featured_snippet_owned" => "yes",
            "search_intention" => "transactional",
            "current_ranking_position" => "1",
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('keywords', [
            'keyword' => 'test_keyword_for_delete'
        ]);
    }

    public function test_show()
    {
        Keyword::whereKeyword('test_keyword_for_delete')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $keyword = Keyword::create([
            'keyword' => 'test_keyword_for_delete',
            "search_volume" => 1,
            "search_volume_clustered" => 1,
            "current_ranking_url" => "https://www.site.com/page",
            "featured_snippet_keyword" => "yes",
            "featured_snippet_owned" => "yes",
            "search_intention" => "transactional",
            "current_ranking_position" => "1",
        ]);

        $response = $this->actingAs($user)->get('/api/keywords/' . $keyword->id);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('keyword')->missing('message');
            })
            ->assertStatus(200);
    }


    public function test_show_non_auth()
    {
        $response = $this->get('/api/keywords/' . 1);
        $response->assertStatus(401);
    }

    public function test_show_not_found()
    {
        Keyword::whereKeyword('test_keyword_for_delete')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $last = DB::table('keywords')->latest('id')->first();
        $id =  $last ? $last->id : 0;

        $response = $this->actingAs($user)->get('/api/keywords/' . ++$id);

        $response->assertStatus(404);
    }

    public function test_update()
    {
        Keyword::whereKeyword('test_keyword_for_delete')->delete();
        Keyword::whereKeyword('updated_test_keyword_for_delete')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $keyword = Keyword::create([
            'keyword' => 'test_keyword_for_delete',
            "search_volume" => 1,
            "search_volume_clustered" => 1,
            "current_ranking_url" => "https://www.site.com/page",
            "featured_snippet_keyword" => "yes",
            "featured_snippet_owned" => "yes",
            "search_intention" => "transactional",
            "current_ranking_position" => "Not in top 100",
        ]);

        $response = $this->actingAs($user)->put('/api/keywords/' . $keyword->id, [
            "current_ranking_url" => "https://www.site2.com/page"
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('keyword')->missing('message');
            })
            ->assertStatus(200);

        $this->assertDatabaseHas('keywords', [
            'id' => $keyword->id,
            'keyword' => 'test_keyword_for_delete',
            "current_ranking_url" => "https://www.site2.com/page"
        ]);

        Keyword::whereKeyword('updated_test_keyword_for_delete')->delete();
    }

    public function test_update_non_auth()
    {
        Keyword::whereKeyword('test_keyword_for_delete')->delete();

        $keyword = Keyword::create([
            'keyword' => 'test_keyword_for_delete',
            "search_volume" => 1,
            "search_volume_clustered" => 1,
            "current_ranking_url" => "https://www.site.com/page",
            "featured_snippet_keyword" => "yes",
            "featured_snippet_owned" => "yes",
            "search_intention" => "transactional",
            "current_ranking_position" => "1",
        ]);

        $response = $this->put('/api/keywords/' . $keyword->id, [
            "current_ranking_url" => "https://www.site2.com/page",
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('keywords', [
            'id' => $keyword->id,
            'keyword' => 'test_keyword_for_delete',
            "current_ranking_url" => "https://www.site2.com/page",
        ]);
    }

    public function test_update_not_found()
    {
        Keyword::whereKeyword('test_keyword_for_delete')->delete();
        $user = User::whereEmail('admin@loc')->first();
        $last = DB::table('keywords')->latest('id')->first();
        $id =  $last ? $last->id : 0;

        $response = $this->actingAs($user)->put('/api/keywords/' . ++$id, [
            'keyword' => 'you_never_see_me',
        ]);
        $response->assertStatus(404);

        Keyword::whereKeyword('test_keyword_for_delete')->delete();
    }

    public function test_destroy()
    {
        Keyword::whereKeyword('test_keyword_for_delete')->delete();

        $user = User::whereEmail('admin@loc')->first();

        $keyword = Keyword::create([
            'keyword' => 'delete_me',
            "search_volume" => 1,
            "search_volume_clustered" => 1,
            "current_ranking_url" => "https://www.site.com/page",
            "featured_snippet_keyword" => "yes",
            "featured_snippet_owned" => "yes",
            "search_intention" => "transactional",
            "current_ranking_position" => "1",
        ]);

        $response = $this->actingAs($user)->delete('/api/keywords/' . $keyword->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('keywords', [
            'id' => $keyword->id,
            'keyword' => 'test_keyword_for_delete',
        ]);
    }


    public function test_destroy_non_auth()
    {
        Keyword::whereKeyword('test_keyword_for_delete')->delete();

        $keyword = Keyword::create([
            'keyword' => 'test_keyword_for_delete',
            "search_volume" => 1,
            "search_volume_clustered" => 1,
            "current_ranking_url" => "https://www.site.com/page",
            "featured_snippet_keyword" => "yes",
            "featured_snippet_owned" => "yes",
            "search_intention" => "transactional",
            "current_ranking_position" => "1",
        ]);

        $response = $this->delete('/api/keywords/' . $keyword->id);

        $response->assertStatus(401);

        $this->assertDatabaseHas('keywords', [
            'id' => $keyword->id,
            'keyword' => 'test_keyword_for_delete',
        ]);
        Keyword::whereKeyword('test_keyword_for_delete')->delete();
    }

    public function test_destroy_not_found()
    {
        $user = User::whereEmail('admin@loc')->first();
        $last = DB::table('keywords')->latest('id')->first();
        $id =  $last ? $last->id : 0;

        $response = $this->actingAs($user)->delete('/api/keywords/' . ++$id);
        $response->assertStatus(404);
    }

}
