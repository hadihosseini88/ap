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

\Illuminate\Support\Facades\Route::group(['namespace' => '\Laravel\Passport\Http\Controllers', 'middleware' => ['throttle']], function ($router) {
    $router->post('login', [
        'as' => 'auth.login',
        'uses' => 'AccessTokenController@issueToken'
    ]);
});

Route::group([], function ($router) {
    $router->post('register', [
        'as' => 'auth.register',
        'uses' => 'AuthController@register'
    ]);
});

Route::post('resend-verification-code', [
    'as' => 'auth.register.resend.verification.code',
    'uses' => 'AuthController@resendVerificationCode'
]);

Route::group([], function ($router) {
    $router->post('register', [
        'as' => 'auth.register',
        'uses' => 'AuthController@register'
    ]);
});

Route::post(
    'change-email',
    [
        'as' => 'change.email',
        'uses' => 'UserController@changeEmail'
    ])->middleware(['auth:api']);

Route::post(
    'change-email-submit',
    [
        'as' => 'change.email.submit',
        'uses' => 'UserController@changeEmailSubmit'
    ])->middleware(['auth:api']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
