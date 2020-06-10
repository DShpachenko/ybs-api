<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Exceptions\RegistrationException;
use App\Modules\Auth\Models\SmsCode;
use App\Modules\Auth\Repositories\SmsCodeRepository;
use App\Modules\Auth\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

/**
 * Сервис регистрации.
 *
 * Class RegistrationService
 * @package App\Modules\Auth\Services
 */
class RegistrationService
{
    /**
     * @var UserRepository
     */
    public $userRepository;

    /**
     * @var SmsCodeRepository
     */
    public $smsCodeRepository;

    /**
     * RegistrationService constructor.
     * @param UserRepository $userRepository
     * @param SmsCodeRepository $smsCodeRepository
     */
    public function __construct(UserRepository $userRepository, SmsCodeRepository $smsCodeRepository)
    {
        $this->userRepository = $userRepository;
        $this->smsCodeRepository = $smsCodeRepository;
    }

    /**
     * Начальная стадия регистрации.
     *
     * @param $params
     * @return array|null
     * @throws RegistrationException
     */
    public function registrationStart($params): ? array
    {
        DB::beginTransaction();

        if ($id = $this->userRepository->registrationNewUser($params)) {
            if ($code = $this->smsCodeRepository->createNewSmsCode($id, SmsCode::TYPE_REGISTRATION)) {
                DB::commit();

                return [
                    'status' => 'success',
                    'code' => $code,
                ];
            }
        }

        DB::rollBack();

        throw new RegistrationException(__('exception.server_error'));
    }

    /**
     * Подтверждение регистрации.
     *
     * @param $params
     * @return array|null
     * @throws RegistrationException
     */
    public function registrationConfirm($params): ? array
    {
        $user = $this->userRepository->findByPhone($params['phone']);

        DB::beginTransaction();

        if ($user && $code = $this->smsCodeRepository->checkCode($user->id, $params['code'], SmsCode::TYPE_REGISTRATION)) {
            $this->userRepository->confirmUser($user);
            $this->smsCodeRepository->changeUsedStatus($code);

            DB::commit();

            return ['status' => 'success'];
        }

        DB::rollBack();

        throw new RegistrationException(__('response.error_failed_sms_code'));
    }
}
