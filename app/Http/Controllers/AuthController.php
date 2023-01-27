<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterNewUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(RegisterNewUserRequest $request)
    {
        $field = $request->has('email') ? 'email' : 'mobile';
        $value = $request->input($field);

        $code = random_int(100000, 999999);

        $expirtion = config('auth.register_cache_expiration', 1440);
        Cache::put('user-auth-register-' . $value, compact('code', 'field'), $expirtion);

        //todo ارسال تایید ایمیل یا پیامک
        Log::info('SEND-REGISTER-CODE-MESSAGE-TO-USER', ['code' => $code]);
        return response(['message' => 'کاربر ثبت موقت شد'], 200);
    }

    public function registerVerify($code, $field)
    {
        $registerData = Cache::get('user-auth-register-' . $field);
        if ($registerData && $registerData['code'] == $code){
            dd('ok');
        }
        dd($code, $field, $registerData);
    }


}
