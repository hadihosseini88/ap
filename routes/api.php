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

Route::group(['namespace'=>'\Laravel\Passport\Http\Controllers','middleware'=>['throttle']], function ($router){
    $router->post('login',[
        'as'=>'auth.login',
        'uses'=> 'AccessTokenController@issueToken'
    ]);
});

\Illuminate\Support\Facades\Route::group([],function ($router){
    $router->post('register',[
        'as'=>'auth.register',
        'uses'=> 'AuthController@register'
    ]);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
