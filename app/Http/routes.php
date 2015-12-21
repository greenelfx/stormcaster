<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::post('api/user/create', 'UserController@createUser');
Route::post('api/user/auth', 'UserController@authenticate');
Route::get('api/user/disconnect', 'UserController@disconnectAccount');
Route::post('api/user/news/edit', 'UserController@editNews');
Route::get('api/user/news', 'UserController@getEditableNews');
Route::post('api/user/news/delete', 'UserController@deleteNews');


Route::get('api/rankings', 'PageController@getRankings');
Route::get('api/news/archive', 'PageController@getNewsArchive');
Route::get('api/news/{id}', 'PageController@getNewsArticle');