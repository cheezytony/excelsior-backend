<?php

use App\Http\Controllers\API\Authentication\LoginController;
use App\Http\Controllers\API\Authentication\RegisterController;
use App\Http\Controllers\API\Post\CommentController;
use App\Http\Controllers\API\Post\PostController;
use App\Http\Controllers\API\Post\TagController;
use App\Http\Controllers\API\Post\TopicController;
use App\Http\Controllers\API\User\ProfileController;
use App\Http\Controllers\API\User\UserController;
use App\Http\Controllers\API\User\UserPostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([], function () {
    Route::post("register", [RegisterController::class, "store"])
        ->middleware('throttle:authentication');
    Route::post("login", [LoginController::class, "store"])
        ->middleware('throttle:authentication');
    Route::post("logout", [LoginController::class, "destroy"]);
    Route::post("auth/refresh", [LoginController::class, "update"]);
});

Route::group([], function () {
    Route::apiResource('posts', PostController::class)
        ->scoped(['post' => 'slug']);

    Route::apiResource('posts.comments', CommentController::class);

    Route::apiResource('tags', TagController::class)
        ->only(['index', 'show'])
        ->scoped(['tag' => 'slug']);

    Route::apiResource('topics', TopicController::class)
        ->only(['index', 'show'])
        ->scoped(['topic' => 'slug']);

    Route::apiResource('users', UserController::class)
        ->only(['show'])
        ->scoped(['user' => 'username']);

    Route::apiResource('users.posts', UserPostController::class)
        ->only(['index']);

    Route::apiResource('profile', ProfileController::class)
        ->only(['index']);
});
