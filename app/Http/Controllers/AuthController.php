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

        //todo ایجاد کد شش رقمی خودکار
        $code = '123456';
        //todo باید مقدار تاری کش در کانفیگ ست شود
        Cache::put('user-auth-register-' . $value, compact('code','field'), now()->addDays(5));

        //todo ارسال تایید ایمیل یا پیامک
        Log::info('SEND-REGISTER-CODE-MESSAGE-TO-USER', ['code' => $code]);
        return response(['message' => 'کاربر ثبت موقت شد'], 200);
    }
}
