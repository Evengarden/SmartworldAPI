<?php

use App\Http\Controllers\BlackListController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
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
 */
//Users
Route::post('user/authorize', 'App\Http\Controllers\UserController@Authorization');
Route::middleware('auth')->get('user/news', [UserController::class, 'News']);

Route::middleware('auth')->get('user/posts', [UserController::class, 'getPosts']);

Route::middleware('auth')->put('user/{id}', [UserController::class, 'update']);

Route::middleware('auth')->get('user/{id}', [UserController::class, 'show']);

Route::middleware('auth')->post('user/', [UserController::class, 'store']);

Route::middleware('auth')->get('user', [UserController::class, 'index']);

Route::middleware('auth')->get('user/posts', [UserController::class, 'getPosts']);
//Posts
Route::middleware('auth')->get('post', [PostController::class, 'index']);

Route::middleware('auth')->post('post/', [PostController::class, 'store']);

Route::middleware('auth')->get('post/{id}', [PostController::class, 'show']);

Route::middleware('auth')->get('post/{id}', [PostController::class, 'update']);

Route::middleware('auth')->get('post/{id}', [PostController::class, 'destroy']);
//Blacklist
Route::middleware('auth')->get('blacklist', [BlacklistController::class, 'index']);

Route::middleware('auth')->post('blacklist/', [BlacklistController::class, 'store']);

Route::middleware('auth')->get('blacklist/{id}', [BlacklistController::class, 'show']);

Route::middleware('auth')->get('blacklist/{id}', [BlacklistController::class, 'update']);

Route::middleware('auth')->get('blacklist/{id}', [BlacklistController::class, 'destroy']);
//Comment
Route::middleware('auth')->get('comment', [CommentController::class, 'index']);

Route::middleware('auth')->post('comment/', [CommentController::class, 'store']);

Route::middleware('auth')->get('comment/{id}', [CommentController::class, 'show']);

Route::middleware('auth')->get('comment/{id}', [CommentController::class, 'update']);

Route::middleware('auth')->get('comment/{id}', [CommentController::class, 'destroy']);
//Follow
Route::middleware('auth')->get('follow', [FollowController::class, 'index']);

Route::middleware('auth')->post('follow/', [FollowController::class, 'store']);

Route::middleware('auth')->get('follow/{id}', [FollowController::class, 'show']);

Route::middleware('auth')->get('follow/{id}', [FollowController::class, 'update']);

Route::middleware('auth')->get('follow/{id}', [FollowController::class, 'destroy']);

//Check auth
Route::middleware('auth')->get('/me', function (Request $request) {
    return auth()->user();
});
