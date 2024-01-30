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


/**
 * روت های مربوط به auth نگهداری می کند
 */
Route::group([], function ($router) {
    \Illuminate\Support\Facades\Route::group(['namespace' => '\Laravel\Passport\Http\Controllers', 'middleware' => ['throttle']], function ($router) {
        $router->post('login', [
            'as' => 'auth.login',
            'uses' => 'AccessTokenController@issueToken'
        ]);
    });

    $router->post('register', [
        'as' => 'auth.register',
        'uses' => 'AuthController@register'
    ]);

    $router->post('register-verify', [
        'as' => 'auth.register.verify',
        'uses' => 'AuthController@registerVerify'
    ]);

    $router->post('resend-verification-code', [
        'as' => 'auth.register.resend.verification.code',
        'uses' => 'AuthController@resendVerificationCode'
    ]);

});


/**
 * روتهای مربوط به user
 */
Route::group(['middleware' => ['auth:api']], function ($router) {
    $router->post(
        'change-email',
        [
            'as' => 'change.email',
            'uses' => 'UserController@changeEmail'
        ]);

    $router->post(
        'change-email-submit',
        [
            'as' => 'change.email.submit',
            'uses' => 'UserController@changeEmailSubmit'
        ]);

});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/channel'], function ($router) {
    $router->put('/{id?}', [
        'as' => 'channel.update',
        'uses' => 'ChannelController@update'
    ]);

});

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
