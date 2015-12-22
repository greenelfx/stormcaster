<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
*/

$app->get('/', function () {
    return view('welcome');
});
$app->get('key', function() {
    return str_random(32);
});
/*
* UserController Routes
*/
$app->get('api/user/editnews', 'UserController@getEditableNews');
$app->get('api/user/disconnect', 'UserController@disconnectAccount');

$app->post('api/user', 'UserController@createUser');
$app->post('api/user/auth', 'UserController@authenticate');
$app->post('api/user/news/edit', 'UserController@editNews');
$app->post('api/user/news/delete', 'UserController@deleteNews');
$app->post('api/user/news', 'UserController@createNews');
$app->post('api/user/vote', 'UserController@submitVote');

/*
* PageController Routes
*/
$app->get('api/rankings', 'PageController@getRankings');
$app->get('api/news/archive', 'PageController@getNewsArchive');
$app->get('api/news/{id}', 'PageController@getNewsArticle');