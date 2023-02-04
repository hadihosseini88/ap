<?php

if (!function_exists('to_valid_mobile_number')){
    /**
     * استاندارد سازی شماره تلفن با شروع +98
     * @param string $mobile شماره تلفن
     * @return string
     */
    function to_valid_mobile_number(string $mobile)
    {
        return '+98' . substr($mobile, -10, 10);
    }
}

if (!function_exists('random_verification_code')){
    /**
     * ایجاد کد تصادفی برای ثبت نام
     * @return int
     * @throws Exception
     */
    function random_verification_code()
    {
        return random_int(100000, 999999);
    }
}
