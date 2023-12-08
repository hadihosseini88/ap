<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Exceptions\RegisterVerificationException;
use App\Exceptions\UserAlreadyRegisteredException;
use App\Http\Requests\Auth\RegisterNewUserRequest;
use App\Http\Requests\Auth\RegisterVerifyUserRequest;
use App\Http\Requests\Auth\ResendVerificationCodeRequest;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterNewUserRequest $request)
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
            Log::error($exception);
            DB::rollBack();
            return response(['message'=>'خطایی رخ داده است']);
        }
    }

    public function registerVerify(RegisterVerifyUserRequest $request)
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

    public function resendVerificationCode(ResendVerificationCodeRequest $request)
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


}
