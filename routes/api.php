<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('rankings', 'PageController@getRankings');
Route::get('online', 'PageController@getOnlineCount');
Route::get('posts/all', 'PageController@getNewsArchive');
Route::get('posts/recent', 'PageController@getRecentPosts');
Route::get('posts/{post}', 'PageController@getNewsArticle');

Route::post('user/register', 'AuthController@register');
Route::post('user/auth', 'AuthController@authenticate');

Route::group(['prefix' => 'user', 'middleware' => ['jwt.auth']], function() {
	Route::get('disconnect', 'UsersController@disconnectAccount');
	Route::post('update', 'UsersController@updateAccount');
});

Route::group(['prefix' => 'admin', 'middleware' => ['jwt.auth', 'checkadmin']], function() {
	Route::post('post/{post}/edit', 'AdminController@editPost');
	Route::post('post/create', 'AdminController@createPost');
	Route::post('post/{post}/delete', 'AdminController@deletePost');
	Route::get('numAccounts', 'AdminController@getNumAccounts');
});