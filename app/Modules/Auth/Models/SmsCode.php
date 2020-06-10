<?php

namespace App\Modules\Auth\Models;

use App\Modules\Auth\Observers\SmsCodeObserver;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SmsCode.
 *
 * @package App\Modules\Auth\Models
 * @property int $id
 * @property int $status
 * @property int $type
 * @property int $user_id
 * @property int $code
 * @property int $created_at
 * @property int $updated_at
 */
class SmsCode extends Model
{
    /**
     * Время жизни (актуальности) смс кода.
     */
    public const LIFE_TIME = 300;

    /**
     * Количество секунд до повторной отправки SMS сообщения.
     */
    public const SECONDS_BEFORE_NEXT = 50;

    /**
     * Тип регистрация.
     */
    public const TYPE_REGISTRATION = 0;

    /**
     * Тип восстановление пароля.
     */
    public const TYPE_PASSWORD_RECOVERY = 1;

    /**
     * Статус новые, не использованный.
     */
    public const STATUS_NEW = 0;

    /**
     * Статус использованный.
     */
    public const STATUS_USED = 1;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sms_code';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'code',
        'status',
        'type',
    ];

    /**
     * Подключениие Observer для модели пользователя.
     */
    public static function boot(): void
    {
        parent::boot();
        static::observe(new SmsCodeObserver);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Modules\Auth\Models\User::class);
    }
}
