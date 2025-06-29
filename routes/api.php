<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PageController;

Route::controller(AuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class);
    Route::get('/post/search', [PostController::class, 'search']);
    Route::get('/post/id/{id}', [PostController::class, 'showById']);
    Route::apiResource('categories', CategoryController::class);
    Route::get('/category/search', [CategoryController::class, 'search']);
    Route::get('/category/id/{uuid}', [CategoryController::class, 'showById']);
    Route::apiResource('pages', PageController::class);
    Route::get('/page/search', [PageController::class, 'search']);
    Route::get('/page/id/{uuid}', [PageController::class, 'showById']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
