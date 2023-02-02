<?php

namespace App\Http\Controllers;

use App\Exceptions\RegisterVerificationException;
use App\Http\Requests\Auth\RegisterNewUserRequest;
use App\Http\Requests\Auth\RegisterVerifyUserRequest;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        Cache::put('user-auth-register-' . $value, compact('code', 'field'), now()->addMinutes($expirtion));

        $user = User::query()->create([
            $field => $value,
            'verify_code' => $code,
        ]);

        //todo ارسال تایید ایمیل یا پیامک
        Log::info('SEND-REGISTER-CODE-MESSAGE-TO-USER', ['code' => $code]);
        return response(['message' => 'کاربر ثبت موقت شد'], 200);
    }

    public function registerVerify(RegisterVerifyUserRequest $request)
    {
        $code = $request->code;

        $user = User::query()->where('verify_code',$code)->first();

        if (empty($user)){
            throw new ModelNotFoundException('کاربری پیدا نشد');
        }

        $user->verify_code = null;
        $user->verified_at = now();
        $user->save();

        return response($user,200);

    }


}
