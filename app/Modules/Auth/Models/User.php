<?php

namespace App\Modules\Auth\Models;

use App\Modules\Auth\Observers\UserObserver;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package App\Modules\Auth\Models
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $password
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class User extends Model
{
    /**
     * Статусы.
     */
    public const STATUS_NEW = 0;
    public const STATUS_CONFIRMED = 1;
    public const STATUS_BLOCKED = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];
    /**
     * @var mixed
     */

    /**
     * Подключениие Observer для модели пользователя.
     */
    public static function boot(): void
    {
        parent::boot();
        static::observe(new UserObserver);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tokens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Modules\Auth\Models\RefreshTokens::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function smsCodes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Modules\Auth\Models\SmsCode::class, 'user_id');
    }
}
