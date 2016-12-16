<?php

use Illuminate\Http\Request;

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

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

Route::get('/rankings', 'PageController@getRankings');
Route::get('/news/all', 'PageController@getNewsArchive');
Route::get('/news/{id}', 'PageController@getNewsArticle');

Route::post('/user/register', 'AuthController@register');
Route::post('/user/auth', 'AuthController@authenticate');

Route::group(array('prefix' => 'user'), function() {
	Route::get('disconnect', 'UsersController@disconnectAccount');
});