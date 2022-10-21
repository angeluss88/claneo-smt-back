<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ImportStrategyController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SeoEventController;
use App\Http\Controllers\TableConfigController;
use App\Http\Controllers\UrlController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

//Public routes
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/change-pwd', [AuthController::class, 'changePassword'])->middleware('guest');
Route::post('/forgot-pwd', [AuthController::class, 'forgotPassword'])->middleware('guest');

//Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('role:admin');
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/account', [AccountController::class, 'show']);
    Route::post('/account/edit', [AccountController::class, 'edit']);

    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('role:admin');
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('role:admin');

    Route::resource('roles', RoleController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('seo_events', SeoEventController::class);
    Route::resource('urls', UrlController::class);
    Route::get('/urls_aggregation', [UrlController::class, 'urlAggregation']);
    Route::resource('keywords', KeywordController::class);

    Route::post('/import_strategy', [ImportStrategyController::class, 'import'])->middleware('role:admin,SEO,Researcher');
    Route::get('/content_strategy_data', [ImportStrategyController::class, 'csStrategy'])->middleware('role:admin,SEO,Researcher');
    Route::get('/timeline_data', [ImportStrategyController::class, 'timelineData'])->middleware('role:admin,SEO,Researcher');
    Route::get('/urlDetails', [ImportStrategyController::class, 'urlDetails'])->middleware('role:admin,SEO,Researcher');
    Route::get('/urlKeywordDetails', [ImportStrategyController::class, 'urlKeywordDetails'])->middleware('role:admin,SEO,Researcher');
    Route::get('/imports', [ImportStrategyController::class, 'index'])->middleware('role:admin,SEO');
    Route::get('/imports/{import}', [ImportStrategyController::class, 'show'])->middleware('role:admin,SEO');
    Route::get('/expandGA/{import}', [ImportStrategyController::class, 'expandGA'])->middleware('role:admin,SEO');
    Route::get('/expandGAForProject/{project}', [ImportStrategyController::class, 'expandGAForProject'])->middleware('role:admin,SEO');
    Route::get('/expandGSC/{import}', [ImportStrategyController::class, 'expandGSC'])->middleware('role:admin,SEO');
    Route::get('/expandGSCForProject/{project}', [ImportStrategyController::class, 'expandGSCForProject'])->middleware('role:admin,SEO');
    Route::get('/getGscAuthLink', [ImportStrategyController::class, 'getGscAuthLink'])->middleware('role:admin,SEO');
    Route::get('/import_example', [ImportStrategyController::class, 'example']);
    Route::get('/events', [EventController::class, 'index'])->middleware('role:admin');
    Route::get('/table_config/{table}', [TableConfigController::class, 'index']);
    Route::post('/table_config', [TableConfigController::class, 'store']);
    Route::delete('/table_config/{table}', [TableConfigController::class, 'destroy']);
    Route::post('/import_urls', [UrlController::class, 'import']);
    Route::get('/import_urls_example', [UrlController::class, 'example']);
});


