<?php

namespace App\Modules\Auth\Repositories;

use App\Modules\Auth\Models\SmsKey;
use App\Repositories\Repository;

/**
 * Class SmsKeyRepository
 * @package App\Modules\Auth\Repositories
 */
class SmsKeyRepository extends Repository
{
    /**
     * @return mixed|string
     */
    public function model()
    {
        return SmsKey::class;
    }

    /**
     * Генерация смс кода.
     *
     * @return int
     * @throws \Exception
     */
    public static function generateKey(): int
    {
        return random_int(1000, 9999);
    }

    /**
     * Создание нового смс кода.
     *
     * @param $userId
     * @param $type
     * @return int|null
     */
    public function createNewSmsKey($userId, $type): ? int
    {
        if ($sms = $this->create([
            'type' => $type,
            'user_id' => $userId,
        ])) {
            return $sms->key;
        }

        return null;
    }

    /**
     * Проверка смс кода.
     *
     * @param $userId
     * @param $key
     * @param $type
     * @return SmsKey|null
     */
    public function checkKey($userId, $key, $type): ? SmsKey
    {
        $key = $this->model->where('user_id', $userId)
            ->where('type', $type)
            ->where('status', SmsKey::STATUS_NEW)
            ->where('key', $key)
            ->orderBy('id', 'DESC')
            ->first();

        if ($key && (time() - $key->created_at->getTimestamp()) <= SmsKey::LIFE_TIME) {
            return $key;
        }

        return null;
    }

    /**
     * Присвоение статуса использованной смс
     *
     * @param SmsKey $key
     */
    public function changeUsedStatus($key): void
    {
        $key->status = SmsKey::STATUS_USED;
        $key->save();
    }
}
