<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ChangeEmailRequest;
use App\Http\Requests\User\ChangeEmailSubmitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    const CHANGE_EMAIL_CACHE_KEY = 'change.email.for.user.';
    public function changeEmail(ChangeEmailRequest $request)
    {
        try {
            $email = $request->email;
            $userId = auth()->id();
            $code = random_verification_code();
            $expireDate = now()->addMinutes(config('auth.change_email_cache_expiration', 1440));
            Cache::put(self::CHANGE_EMAIL_CACHE_KEY . $userId, compact('email','code'), $expireDate);

            //todo: ارسال ایمیل به کاربر

            Log::info('SEND-CHANGE-EMAIL-CODE: ', compact('code'));

            return response([
                'message' => 'کد تغییر ایمیل با موفقیت به صندوق دریافتی ارسال شد.'
            ],200);
        }
        catch (\Exception $e) {
            Log::error($e);
            return response([
                'message' => 'خطایی رخ داده و سرور قادر به ارسال کد برای تغییر ایمیل نمی باشد.'
            ],500);
        }
    }

    public function changeEmailSubmit(ChangeEmailSubmitRequest $request)
    {
        $userId = auth()->id();
        $cacheKey = self::CHANGE_EMAIL_CACHE_KEY. $userId;
        $cache = Cache::get($cacheKey);

        if (empty($cache) || $cache['code'] != $request->code){
            return response([
                'message' => 'درخواست نامعتبر است'
            ],400);
        }

        $user = auth()->user();
        $user->email = $cache['email'];
        $user->save();

        Cache::forget($cacheKey);

        return response([
            'message' => 'درخواست تغییر ایمیل با موفقیت انجام شد.'
        ],200);

    }
}
