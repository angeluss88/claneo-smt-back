<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ImportStrategyController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
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
    Route::resource('urls', UrlController::class);
    Route::resource('keywords', KeywordController::class);

    Route::post('/import_strategy', [ImportStrategyController::class, 'import'])->middleware('role:admin,SEO,Researcher');
    Route::get('/expandGA/', [ImportStrategyController::class, 'expandGA'])->middleware('role:admin,SEO,Researcher');
});


