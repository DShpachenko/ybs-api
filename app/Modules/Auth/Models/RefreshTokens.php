<?php

namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RefreshTokens
 * @package App\Modules\Auth\Models
 * @property integer $id
 * @property string $user_id
 * @property string $token
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 */
class RefreshTokens extends Model
{
    /**
     * Статусы.
     */
    public const STATUS_DISABLED = 0;
    public const STATUS_ACTIVE = 1;

    /**
     * Время жизни Access токена.
     */
    public const ACCESS_TOKEN_LIFE_TIME = 600;

    /**
     * Время жизни Refresh токена (секунды).
     */
    public const REFRESH_TOKEN_LIFE_TIME = 864000;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token',
        'status',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Modules\Auth\Models\User::class);
    }
}
