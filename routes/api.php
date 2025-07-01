<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\PageController;

Route::controller(AuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class);
    Route::get('/post/id/{uuid}', [PostController::class, 'showById']);
    Route::apiResource('categories', CategoryController::class);
    Route::get('/category/id/{uuid}', [CategoryController::class, 'showById']);
    Route::apiResource('pages', PageController::class);
    Route::get('/page/id/{uuid}', [PageController::class, 'showById']);

    Route::post('logout', [AuthController::class, 'logout']);

});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
