<?php

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
// Route::post('user/info','App\Http\Controllers\UserController@getProfileInfo');

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('user/authorize','App\Http\Controllers\UserController@Authorization');

Route::get('user/news','App\Http\Controllers\UserController@News');

Route::get('user/posts','App\Http\Controllers\UserController@getPosts');

Route::get('user/profile_info','App\Http\Controllers\UserController@getProfileInfo');

// Route::middleware('auth:api')->get('user/profile_info', [UserController::class, 'getProfileInfo']);//показать профиль авторизовнного пользователя

// //все для постов
// Route::middleware('auth:api')->get('posts', [PostController::class, 'show']);// просмотр своих постов авторизованным пользователем
// Route::middleware('auth:api')->get('posts/{id}', [PostController::class, 'show']);// просмотр чужих постов авторизованным пользователем
// Route::middleware('auth:api')->post('posts', [PostController::class, 'store']);// создание поста авторизованным пользователем
// Route::middleware('auth:api')->delete('posts/{id}', [PostController::class, 'destroy']);// удаление поста авторизованным пользователем
// Route::middleware('auth:api')->get('users/wall', [PostController::class, 'wall']); 

// //все для коментов
// Route::middleware('auth:api')->post('comments', [CommentController::class, 'store']);//создание коментария
// Route::middleware('auth:api')->delete('comments/{id}', [CommentController::class, 'destroy']); //удаление коментария


// //все для блэк листа
// Route::middleware('auth:api')->get('blackList/{id}', [BlackListController::class, 'check']);// проверка на нахождение в черном списке
// Route::middleware('auth:api')->post('blackList', [BlackListController::class, 'store']); //добавление в черный список

// //все для подписания
// Route::middleware('auth:api')->post('subscription', [SubscriptionController::class, 'store']);//подписапться на кого либо

Route::resource('user', 'App\Http\Controllers\UserController');

Route::resource('post', 'App\Http\Controllers\PostController');

Route::resource('follower', 'App\Http\Controllers\FollowerController');

Route::resource('comment', 'App\Http\Controllers\CommentController');

Route::resource('blacklist', 'App\Http\Controllers\BlacklistController');


Route::middleware('auth')->get('/me', function (Request $request) {
    return auth()->user();
});
