<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    //Return latest api route
    return redirect()->route('v1_0');
});

$app->group([
    'prefix' => 'v1.0',
    'namespace' => 'V1_0',
], function () use ($app) {

    $app->get('/', ['as' => 'v1_0', function () {
        return "Welcome to Lux";
    }]);

    $app->group([
        'prefix' => 'auth'
    ], function() use ($app){
        $app->post('/signin', 'AuthController@signIn');
        $app->post('/signup', 'AuthController@signUp');

        $app->post('/signin/{snsType:[a-z_]+}', 'AuthController@signInWithApp');
        $app->post('/signup/{snsType:[a-z_]+}', 'AuthController@signUpWithApp');

        $app->put('/refresh', 'AuthController@refreshToken');
        $app->get('/info/{accountIdx:[0-9+]}', 'AuthController@getAuthInfo');

        $app->delete('/{accountIdx?}','AuthController@deleteAccount');
    });

});

