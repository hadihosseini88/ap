<?php

namespace App\Services;

use App\Exceptions\UserAlreadyRegisteredException;
use App\Http\Requests\User\ChangeEmailRequest;
use App\Http\Requests\User\ChangeEmailSubmitRequest;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService
{
    const CHANGE_EMAIL_CACHE_KEY = 'change.email.for.user.';
    public static function registerNewUser(Request $request)
    {
        try {
            DB::beginTransaction();
            $field = $request->getFieldName();
            $value = $request->getFieldValue();

            // اگر کاربر ثبت نام کرده باشد دو حالت داره که یا کد زده یا نه
            if ($user = User::query()->where($field, $value)->first()) {
                // اگر کاربر از قبل ثبت نام خودش را کامل کرده باشه داخل بلاک پایین مبره
                if ($user->verified_at) {
                    throw new UserAlreadyRegisteredException('شما قبلا ثبت نام کرده اید');
                }
                return response(['message' => 'کد فعالسازی قبلا ارسال شده'], 200);
            }

            $code = random_verification_code();
            $user = User::query()->create([
                $field => $value,
                'verify_code' => $code,
            ]);



            //todo ارسال تایید ایمیل یا پیامک
            Log::info('SEND-REGISTER-CODE-MESSAGE-TO-USER', ['code' => $code]);
            DB::commit();
            return response(['message' => 'کاربر ثبت موقت شد'], 200);
        } catch (\Exception $exception) {
            //TODO handel UserAlreadyRegisteredException
            DB::rollBack();
            if($exception instanceof UserAlreadyRegisteredException){
                throw $exception;
            }
            Log::error($exception);
            return response(['message'=>'خطایی رخ داده است']);
        }
    }

    public static function registerNewUserVerify(Request $request)
    {
        $field = $request->getFieldName();
        $code = $request->code;

        $user = User::query()->where([
            $field => $request->input($field),
            'verify_code' => $code,

        ])->first();

        if (empty($user)) {
            throw new ModelNotFoundException('کاربری با کد مورد نظر پیدا نشد');
        }

        $user->verify_code = null;
        $user->verified_at = now();
        $user->save();

        return response($user, 200);
    }

    public static function resendVerificationCodeToUser(Request $request)
    {
        $field = $request->getFieldName();
        $value = $request->getFieldValue();
        $user = User::query()->where($field, $value)->whereNull('verified_at')->first();
        if (!empty($user)) {
            $dateDiff = now()->diffInMinutes($user->updated_at);
            // اگر مدت زمان ارسال کد گذشته بود کد جدید ارسال می شود در غیر اینصورت کد قدیم مجددا ارسال می شود
            if ($dateDiff > config('auth.resend_verification_code_time_diff')) {
                $user->verify_code = random_verification_code();
                $user->save();
            }

            //todo ارسال تایید ایمیل یا پیامک
            Log::info('RESEND-REGISTER-CODE-MESSAGE-TO-USER', ['code' => (int)$user->verify_code]);
            return response(['message' => 'کد برای شما مجددا ارسال گردید'], 200);
        }
        throw new ModelNotFoundException('کاربری با این مشخصات یافت نشد یا قبلا فعالسازی شده است');
    }

    public static function changeEmail(ChangeEmailRequest $request)
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

    public static function changeEmailSubmit(ChangeEmailSubmitRequest $request)
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
