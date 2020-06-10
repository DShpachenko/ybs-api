<?php

namespace App\Modules\Auth\Observers;

use App\Modules\Auth\Models\SmsCode;
use App\Modules\Auth\Repositories\SmsCodeRepository;

/**
 * Класс наблюдатель за моделбю SmsCode.
 *
 * Class SmsCodeObserver
 * @package App\Modules\Auth\Observers
 */
class SmsCodeObserver
{
    /**
     * Обработка события перед сохранением.
     *
     * @param $smsCode
     * @throws \Exception
     */
    public function creating($smsCode): void
    {
        $smsCode->status = SmsCode::STATUS_NEW;
        $smsCode->code = SmsCodeRepository::generateCode();
    }
}
