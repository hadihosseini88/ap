<?php

/**
 * استاندارد سازی شماره تلفن با شروع +98
 * @param string $mobile شماره تلفن
 * @return string
 */
function to_valid_mobile_number(string $mobile)
{
    return '+98' . substr($mobile, -10, 10);
}
