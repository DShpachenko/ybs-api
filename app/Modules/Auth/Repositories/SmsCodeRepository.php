<?php

namespace App\Modules\Auth\Repositories;

use App\Modules\Auth\Models\SmsCode;
use App\Repositories\Repository;

/**
 * Class SmsCodeRepository
 * @package App\Modules\Auth\Repositories
 */
class SmsCodeRepository extends Repository
{
    /**
     * @return mixed|string
     */
    public function model()
    {
        return SmsCode::class;
    }

    /**
     * Генерация смс кода.
     *
     * @return int
     * @throws \Exception
     */
    public static function generateCode(): int
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
    public function createNewSmsCode($userId, $type): ? int
    {
        if ($sms = $this->create([
            'type' => $type,
            'user_id' => $userId,
        ])) {
            return $sms->code;
        }

        return null;
    }

    /**
     * Проверка смс кода.
     *
     * @param $userId
     * @param $code
     * @param $type
     * @return SmsCode|null
     */
    public function checkCode($userId, $code, $type): ? SmsCode
    {
        $code = $this->model->where('user_id', $userId)
            ->where('type', $type)
            ->where('status', SmsCode::STATUS_NEW)
            ->where('code', $code)
            ->orderBy('id', 'DESC')
            ->first();

        if ($code && (time() - $code->created_at->getTimestamp()) <= SmsCode::LIFE_TIME) {
            return $code;
        }

        return null;
    }

    /**
     * Присвоение статуса использованной смс
     *
     * @param SmsCode $code
     */
    public function changeUsedStatus($code): void
    {
        $code->status = SmsCode::STATUS_USED;
        $code->save();
    }
}
