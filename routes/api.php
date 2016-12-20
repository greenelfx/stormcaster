<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('rankings', 'PageController@getRankings');
Route::get('online', 'PageController@getOnlineCount');
Route::get('news/all', 'PageController@getNewsArchive');
Route::get('news/{id}', 'PageController@getNewsArticle');

Route::post('user/register', 'AuthController@register');
Route::post('user/auth', 'AuthController@authenticate');

Route::group(['prefix' => 'user', 'middleware' => ['jwt.auth']], function() {
	Route::get('disconnect', 'UsersController@disconnectAccount');
	Route::post('update', 'UsersController@updateAccount');
});

Route::group(['prefix' => 'admin', 'middleware' => ['jwt.auth', 'checkadmin']], function() {
	Route::post('post/{id}/edit', 'AdminController@editPost');
	Route::post('post/create', 'AdminController@createPost');
	Route::post('post/{id}/delete', 'AdminController@deletePost');
	Route::get('numAccounts', 'AdminController@getNumAccounts');
});