<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;
    const TYPE_USER = 'user';
    const TYPE_ADMIN = 'admin';
    const TYPES = [self::TYPE_USER, self::TYPE_ADMIN];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type','mobile', 'email', 'name', 'password', 'avatar', 'website', 'verify_code', 'verified_at'
        ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'verify_code',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * پیدا کردن کاربر از طریق موبایل یا ایمیل
     * @param $username
     * @return Builder|Model|object|null
     */
    public function findForPassport($username)
    {
        $user = static::query()->where('mobile',$username)->orWhere('email',$username)->first();
        return $user;
    }

    public function setMobileAttribute($value)
    {
        $mobile = '+98'. substr($value, -10,10);
        $this->attributes['mobile'] = $mobile;
    }
}
