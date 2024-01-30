<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ChangeEmailRequest;
use App\Http\Requests\User\ChangeEmailSubmitRequest;
use App\Services\UserService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class UserController extends Controller
{

    /**
     * تغییر ایمیل کاربر
     * @param ChangeEmailRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function changeEmail(ChangeEmailRequest $request)
    {
        return UserService::changeEmail($request);
    }

    /**
     * تایید تغییر ایمیل کاربر
     * @param ChangeEmailSubmitRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function changeEmailSubmit(ChangeEmailSubmitRequest $request)
    {
        return UserService::changeEmailSubmit($request);

    }
}
