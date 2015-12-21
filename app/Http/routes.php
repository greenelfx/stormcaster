<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
* UserController Routes
*/
Route::get('api/user/news', 'UserController@getEditableNews');
Route::get('api/user/disconnect', 'UserController@disconnectAccount');

Route::post('api/user', 'UserController@createUser');
Route::post('api/user/auth', 'UserController@authenticate');
Route::post('api/user/news/edit', 'UserController@editNews');
Route::post('api/user/news/delete', 'UserController@deleteNews');
Route::post('api/user/news', 'UserController@createNews');
Route::post('api/user/vote', 'UserController@submitVote');

/*
* PageController Routes
*/
Route::get('api/rankings', 'PageController@getRankings');
Route::get('api/news/archive', 'PageController@getNewsArchive');
Route::get('api/news/{id}', 'PageController@getNewsArticle');