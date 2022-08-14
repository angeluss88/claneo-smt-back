<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_index()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $response = $this->actingAs($user)->get('/api/projects');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('projects.data')->has('projects.total');
            })
            ->assertStatus(200);
        $this->assertEquals(Project::count(), $response->json()['projects']['total']);
    }

    public function test_index_non_auth()
    {
        $response = $this->get('/api/projects');

        $response->assertStatus(401);
    }

    public function test_store()
    {
        Project::whereDomain('test_project_for_delete')->delete();
        Client::whereName('client_for_test_project')->delete();

        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $client = Client::create([
            'name' => 'client_for_test_project'
        ]);

        $response = $this->actingAs($user)->post('/api/projects', [
            'domain' => 'test_project_for_delete',
            'client' => $client->name,
            'strategy' => Project::NO_EXPAND_STRATEGY,
            'expand_gsc' => 0,
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('project');
            })
            ->assertStatus(201);

        $this->assertDatabaseHas('projects', [
            'domain' => 'test_project_for_delete'
        ]);

        $client->delete();
    }

    public function test_store_no_domain_param()
    {
        Project::whereDomain('test_project_for_delete')->delete();

        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $response = $this->actingAs($user)->post('/api/projects', [
            'domain1' => 'test_project_for_delete'
        ]);

        $response->assertStatus(500);

        $this->assertDatabaseMissing('projects', [
            'domain' => 'test_project_for_delete'
        ]);
    }

    public function test_store_exists_domain_param()
    {
        Project::whereDomain('test_project_for_delete')->delete();

        if($domain = Project::first()->domain) {
            $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
            $response = $this->actingAs($user)->post('/api/projects', [
                'domain' => $domain,
            ]);

            $response->assertStatus(500);
        }
    }

    public function test_store_non_auth()
    {
        Project::whereDomain('test_project_for_delete')->delete();

        $response = $this->post('/api/projects', [
            'domain' => 'test_project_for_delete'
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('projects', [
            'domain' => 'test_project_for_delete'
        ]);
    }

    public function test_show()
    {
        Project::whereDomain('test_project_for_delete')->delete();

        Client::whereName('client_for_test_project')->delete();

        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $client = Client::create([
            'name' => 'client_for_test_project'
        ]);

        $project = Project::create([
            'domain' => 'test_project_for_delete',
            'client' => $client->name,
            'strategy' => Project::NO_EXPAND_STRATEGY,
            'expand_gsc' => 0,
        ]);

        $response = $this->actingAs($user)->get('/api/projects/' . $project->id);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('project')->missing('message');
            })
            ->assertStatus(200);

        $client->delete();
    }


    public function test_show_non_auth()
    {
        $response = $this->get('/api/projects/' . 1);
        $response->assertStatus(401);
    }

    public function test_show_not_found()
    {
        Project::whereDomain('test_project_for_delete')->delete();

        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $last = DB::table('projects')->latest('id')->first();
        $id =  $last ? $last->id : 0;

        $response = $this->actingAs($user)->get('/api/projects/' . ++$id);

        $response->assertStatus(404);
    }

    public function test_update()
    {
        Project::whereDomain('test_project_for_delete')->delete();
        Project::whereDomain('updated_test_project_for_delete')->delete();
        Client::whereName('client_for_test_project')->delete();

        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $client = Client::create([
            'name' => 'client_for_test_project'
        ]);

        $project = Project::create([
            'domain' => 'test_project_for_delete',
            'client' => $client->name,
            'strategy' => Project::NO_EXPAND_STRATEGY,
            'expand_gsc' => 0,
        ]);

        $response = $this->actingAs($user)->put('/api/projects/' . $project->id, [
            'domain' => 'updated_test_project_for_delete',
        ]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('project')->missing('message');
            })
            ->assertStatus(200);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'domain' => 'updated_test_project_for_delete',
        ]);

        Project::whereDomain('updated_test_project_for_delete')->delete();
        $client->delete();
    }

    public function test_update_non_auth()
    {
        Project::whereDomain('test_project_for_delete')->delete();

        $project = Project::create([
            'domain' => 'test_project_for_delete',
            'client' => 'test',
            'strategy' => Project::NO_EXPAND_STRATEGY,
            'expand_gsc' => 0,
        ]);

        $response = $this->put('/api/projects/' . $project->id, [
            'domain' => 'updated_test_project_for_delete',
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
            'domain' => 'updated_test_project_for_delete',
        ]);
    }

    public function test_update_not_found()
    {
        Project::whereDomain('test_project_for_delete')->delete();
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $last = DB::table('projects')->latest('id')->first();
        $id =  $last ? $last->id : 0;

        $response = $this->actingAs($user)->put('/api/projects/' . ++$id, [
            'domain' => 'you_never_see_me',
        ]);
        $response->assertStatus(404);

        Project::whereDomain('updated_test_project_for_delete')->delete();
    }

    public function test_destroy()
    {
        Project::whereDomain('test_project_for_delete')->delete();
        Client::whereName('client_for_test_project')->delete();

        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $client = Client::create([
            'name' => 'client_for_test_project'
        ]);

        $project = Project::create([
            'domain' => 'delete_me',
            'client' => $client->name,
            'strategy' => Project::NO_EXPAND_STRATEGY,
            'expand_gsc' => 0,
        ]);

        $response = $this->actingAs($user)->delete('/api/projects/' . $project->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
            'domain' => 'test_project_for_delete',
        ]);

        $client->delete();
    }


    public function test_destroy_non_auth()
    {
        Project::whereDomain('test_project_for_delete')->delete();

        $project = Project::create([
            'domain' => 'test_project_for_delete',
            'client' => 'test',
            'strategy' => Project::NO_EXPAND_STRATEGY,
            'expand_gsc' => 0,
        ]);

        $response = $this->delete('/api/projects/' . $project->id);

        $response->assertStatus(401);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'domain' => 'test_project_for_delete',
        ]);
        Project::whereDomain('test_project_for_delete')->delete();
    }

    public function test_destroy_not_found()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $last = DB::table('projects')->latest('id')->first();
        $id =  $last ? $last->id : 0;

        $response = $this->actingAs($user)->delete('/api/projects/' . ++$id);
        $response->assertStatus(404);
    }

}
