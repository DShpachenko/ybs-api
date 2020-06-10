<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Exceptions\SmsException;
use App\Modules\Auth\Models\SmsCode;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Repositories\SmsCodeRepository;
use App\Modules\Auth\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

/**
 * Сервис повторной отправки смс кода.
 *
 * Class SmsService
 * @package App\Modules\Auth\Services
 */
class SmsService
{
    /**
     * @var SmsCodeRepository
     */
    public $smsCodeRepository;

    /**
     * @var UserRepository
     */
    public $userRepository;

    /**
     * SmsService constructor.
     * @param UserRepository $userRepository
     * @param SmsCodeRepository $smsCodeRepository
     */
    public function __construct(UserRepository $userRepository, SmsCodeRepository $smsCodeRepository)
    {
        $this->smsCodeRepository = $smsCodeRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Повторная отправка смс кода.
     *
     * @param $params
     * @return array|null
     * @throws SmsException
     */
    public function sendSms($params): ? array
    {
        $user = $this->userRepository->findByPhone($params['phone'], [User::STATUS_CONFIRMED, User::STATUS_NEW]);

        if ($user) {
            DB::beginTransaction();

            $type = SmsCode::TYPE_PASSWORD_RECOVERY;

            if ($user->status === User::STATUS_NEW) {
                $type = SmsCode::TYPE_REGISTRATION;
            }

            if ($code = $this->smsCodeRepository->createNewSmsCode($user->id, $type)) {
                DB::commit();

                return [
                    'status' => 'success',
                    'code' => $code,
                ];
            }

            DB::rollBack();

            throw new SmsException(__('exception.server_error'));
        }

        throw new SmsException(__('response.user_not_found'));
    }
}
