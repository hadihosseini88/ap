<?php

namespace App\Http\Controllers;

use App\Exceptions\RegisterVerificationException;
use App\Exceptions\UserAlreadyRegisteredException;
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
        // اگر کاربر ثبت نام کرده باشد دو حالت داره که یا کد زده یا نه
        if ($user = User::query()->where($field, $value)->first()){
            // اگر کاربر از قبل ثبت نام خودش را کامل کرده باشه داخل بلاک پایین مبره
            if ($user->verified_at){
                throw new UserAlreadyRegisteredException('شما قبلا ثبت نام کرده اید');
            }
            return response(['message' => 'کد فعالسازی قبلا ارسال شده'], 200);
        }
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
        $field = $request->has('email') ? 'email' : 'mobile';
        $code = $request->code;

        $user = User::query()->where([
            $field => $request->input($field),
            'verify_code' =>$code,

        ])->first();

        if (empty($user)){
            throw new ModelNotFoundException('کاربری با کد مورد نظر پیدا نشد');
        }

        $user->verify_code = null;
        $user->verified_at = now();
        $user->save();

        return response($user,200);

    }


}
