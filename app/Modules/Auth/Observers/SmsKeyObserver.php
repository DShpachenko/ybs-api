<?php

namespace App\Modules\Auth\Observers;

use App\Modules\Auth\Models\SmsKey;
use App\Modules\Auth\Repositories\SmsKeyRepository;

/**
 * Класс наблюдатель за моделбю SmsKey.
 *
 * Class SmsKeyObserver
 * @package App\Modules\Auth\Observers
 */
class SmsKeyObserver
{
    /**
     * Обработка события перед сохранением.
     *
     * @param $smsKey
     * @throws \Exception
     */
    public function creating($smsKey): void
    {
        $smsKey->status = SmsKey::STATUS_NEW;
        $smsKey->key = SmsKeyRepository::generateKey();
    }
}
