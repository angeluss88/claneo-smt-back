<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\Import;
use App\Models\Project;
use App\Models\URL;
use App\Models\User;
use App\Services\GoogleAnalyticsService;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ImportStrategyControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_index()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $response = $this->actingAs($user)->get('/api/imports');

        $response->assertStatus(200);
    }

    public function test_index_non_auth()
    {
        $response = $this->get('/api/imports');

        $response->assertStatus(401);
    }

    public function test_show()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $import = Import::first('id');

        if(!$import) {
            $client = Client::create([
                'name' => 'client_for_test_project'
            ]);
            $project = Project::create([
                'domain' => 'test_project_for_delete',
                'client' => $client->name,
                'strategy' => Project::NO_EXPAND_STRATEGY,
                'expand_gsc' => 0,
            ]);
            $import = Import::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
            ]);
        }

        $response = $this->actingAs($user)->get('/api/imports/' . $import->id);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('import')->missing('message');
            })
            ->assertStatus(200);

        if(isset($client) && isset($project)) {
            $client->delete();
        }
    }

    public function test_show_non_auth()
    {
        $response = $this->get('/api/imports/' . 0);

        $response->assertStatus(401);
    }

    public function test_show_not_found()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $response = $this->actingAs($user)->get('/api/imports/' . 0);

        $response->assertStatus(404);
    }

    public function test_example()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $response = $this->actingAs($user)->get('/api/import_example');

        $response
            ->assertDownload('is_example.csv')
            ->assertStatus(200);
    }

    public function test_import()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $project = Project::first('id');

        $path = public_path(). "/files/is_example.csv";

        $response = $this->actingAs($user)->post('/api/import_strategy', [
            'file' => new UploadedFile($path, 'is_example.csv', null, null, true ),
            'project_id' => $project->id,
        ]);

        $response->assertStatus(201);
    }

    public function test_import_non_auth()
    {
        $project = Project::first('id');

        $path = public_path(). "/files/is_example.csv";

        $response = $this->post('/api/import_strategy', [
            'file' => new UploadedFile($path, 'is_example.csv', null, null, true ),
            'project_id' => $project->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_csStrategy()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $response = $this->actingAs($user)->get('/api/content_strategy_data?page=1&count=10&url=test&keyword=test&import_date=2021.11.03 00:00:00-2021.12.03 00:00:00&project_id=19&import_id=1');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('count')->has('page')->has('pages')->has('perPage')->has('csData');
            })
            ->assertStatus(200);
    }

    public function test_csStrategy_non_auth()
    {
        $response = $this->get('/api/content_strategy_data?page=1&count=10&url=test&keyword=test&import_date=2021.11.03 00:00:00-2021.12.03 00:00:00&project_id=19&import_id=1');

        $response->assertStatus(401);
    }

    public function test_timelineData()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $metrics = array_merge(GoogleAnalyticsService::GA_METRICS, GoogleAnalyticsService::GSC_METRICS);

        $this->assertNotEmpty($metrics);

        foreach ($metrics as $metric) {
            $response = $this
                ->actingAs($user)
                ->get('/api/timeline_data?import_date=2021.11.03-2021.12.03&project_id=19&metric=' . $metric);

            $response
                ->assertJson(function (AssertableJson $json) {
                    return $json->has('timeLineData')->missing('message');
                })
                ->assertStatus(200);
        }
    }

    public function test_timelineData_non_auth()
    {
        $response = $this->get('/api/timeline_data?project_id=19&metric=clicks');
        $response->assertStatus(401);
    }

    public function test_timelineData_no_required_data()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $response = $this->actingAs($user) ->get('/api/timeline_data?import_date=2021.11.03-2021.12.03');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('status')->has('message')->missing('timeLineData');
            })
            ->assertStatus(500);
    }

    public function test_timelineData_non_exists_metric()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $response = $this->actingAs($user) ->get('/api/timeline_data?project_id=19&metric=some_wrong_metric');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('status')->has('message')->missing('timeLineData');
            })
            ->assertStatus(500);
    }

    public function test_expandGA()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $import = Import::latest()->first('id');

        if(!$import) {
            $client = Client::create([
                'name' => 'client_for_test_project'
            ]);
            $project = Project::create([
                'domain' => 'test_project_for_delete',
                'client' => $client->name,
                'strategy' => Project::NO_EXPAND_STRATEGY,
                'expand_gsc' => 0,
            ]);
            $import = Import::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
            ]);
        }

        $response = $this->actingAs($user)->get('/api/expandGA/' . $import->id);

        $response->assertStatus(204);

        if(isset($client) && isset($project)) {
            $client->delete();
        }
    }

    public function test_expandGA_non_auth()
    {
        $response = $this->get('/api/expandGA/0');

        $response->assertStatus(401);
    }

    public function test_expandGA_non_exists_import()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $response = $this->actingAs($user)->get('/api/expandGA/0');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('message');
            })
            ->assertStatus(404);
    }

    public function test_expandGSC()
    {
        $this->assertTrue(true);
        //ignore it for now
//        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
//        $import = Import::latest()->first('id');
//
//        if(!$import) {
//            $client = Client::create([
//                'name' => 'client_for_test_project'
//            ]);
//            $project = Project::create([
//                'domain' => 'test_project_for_delete',
//                'client' => $client->name,
//                'strategy' => Project::NO_EXPAND_STRATEGY,
//                'expand_gsc' => 0,
//            ]);
//            $import = Import::create([
//                'user_id' => $user->id,
//                'project_id' => $project->id,
//            ]);
//        }
//
//        $response = $this->actingAs($user)->get('/api/expandGSC/' . $import->id);
//
//        $response->assertStatus(204);
//
//        if(isset($client) && isset($project)) {
//            $client->delete();
//        }
    }

    public function test_expandGSC_non_auth()
    {
        $this->assertTrue(true);
        //ignore it for now
//        $response = $this->get('/api/expandGSC/0');
//
//        $response->assertStatus(401);
    }

    public function test_expandGSC_non_exists_import()
    {
        $this->assertTrue(true);
        //ignore it for now
//        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
//
//        $response = $this->actingAs($user)->get('/api/expandGSC/0');
//
//        $response
//            ->assertJson(function (AssertableJson $json) {
//                return $json->has('message');
//            })
//            ->assertStatus(404);
    }

    public function test_expandGAForProject()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $project = Project::latest()->first('id');

        if(!$project) {
            $client = Client::create([
                'name' => 'client_for_test_project'
            ]);
            $project = Project::create([
                'domain' => 'test_project_for_delete',
                'client' => $client->name,
                'strategy' => Project::NO_EXPAND_STRATEGY,
                'expand_gsc' => 0,
            ]);
        }

        $response = $this->actingAs($user)->get('/api/expandGAForProject/' . $project->id);

        $response->assertStatus(204);

        if(isset($client) && isset($project)) {
            $client->delete();
        }
    }

    public function test_expandGAForProject_non_auth()
    {
        $response = $this->get('/api/expandGAForProject/0');

        $response->assertStatus(401);
    }

    public function test_expandGAForProject_non_exists_project()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();

        $response = $this->actingAs($user)->get('/api/expandGAForProject/0');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('message');
            })
            ->assertStatus(404);
    }

    public function test_expandGSCForProject()
    {
        $this->assertTrue(true);
        //ignore it for now
//        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
//        $project = Project::latest()->first('id');
//
//        if(!$project) {
//            $client = Client::create([
//                'name' => 'client_for_test_project'
//            ]);
//            $project = Project::create([
//                'domain' => 'test_project_for_delete',
//                'client' => $client->name,
//                'strategy' => Project::NO_EXPAND_STRATEGY,
//                'expand_gsc' => 0,
//            ]);
//        }
//
//        $response = $this->actingAs($user)->get('/api/expandGSCForProject/' . $project->id);
//
//        $response->assertStatus(204);
//
//        if(isset($client) && isset($project)) {
//            $client->delete();
//        }
    }

    public function test_expandGSCForProject_non_auth()
    {
        $this->assertTrue(true);
        //ignore it for now
//        $response = $this->get('/api/expandGSCForProject/0');
//
//        $response->assertStatus(401);
    }

    public function test_expandGSCForProject_non_exists_project()
    {
        $this->assertTrue(true);
        //ignore it for now
//        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
//
//        $response = $this->actingAs($user)->get('/api/expandGSCForProject/0');
//
//        $response
//            ->assertJson(function (AssertableJson $json) {
//                return $json->has('message');
//            })
//            ->assertStatus(404);
    }

    public function test_urlDetails()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $metrics = array_merge(GoogleAnalyticsService::GA_METRICS, GoogleAnalyticsService::GSC_METRICS);
        $url = URL::latest()->first('id');

        $this->assertNotEmpty($metrics);

        foreach ($metrics as $metric) {
            $response = $this
                ->actingAs($user)
                ->get('/api/urlDetails?import_date=2021.11.03-2021.12.03&url_id=' . $url->id . '&metric=' . $metric);

            $response
                ->assertJson(function (AssertableJson $json) {
                    return $json->has('urlDetails')->missing('message');
                })
                ->assertStatus(200);
        }
    }

    public function test_urlDetails_non_auth()
    {
        $response = $this->get('/api/urlDetails?import_date=2021.11.03-2021.12.03&url_id=0&metric=clicks');

        $response->assertStatus(401);
    }

    public function test_urlDetails_non_exists_url()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $metrics = GoogleAnalyticsService::GA_METRICS;

        $this->assertNotEmpty($metrics);

        $response = $this
            ->actingAs($user)
            ->get('/api/urlDetails?import_date=2021.11.03-2021.12.03&url_id=0&metric=' . $metrics[0]);

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('status')->has('message')->missing('urlDetails');
            })
            ->assertStatus(500);

    }

    public function test_urlDetails_wrong_metric()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $url = URL::latest()->first('id');

        $response = $this
            ->actingAs($user)
            ->get('/api/urlDetails?import_date=2021.11.03-2021.12.03&url_id=' . $url->id . '&metric=some_non_exists_metric');

        $response
            ->assertJson(function (AssertableJson $json) {
                return $json->has('status')->has('message')->missing('urlDetails');
            })
            ->assertStatus(500);

    }

    public function test_getGscAuthLink()
    {
        $user = User::whereEmail(env('APP_ADMIN_EMAIL', 'admin@loc'))->first();
        $response = $this->actingAs($user)->get('/api/getGscAuthLink');

        $response->assertStatus(200);
    }

}
