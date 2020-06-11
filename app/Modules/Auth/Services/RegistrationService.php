<?php

namespace App\Modules\Auth\Services;

use App\Exceptions\JsonRpcException;
use App\Modules\Auth\Exceptions\RegistrationException;
use App\Modules\Auth\Models\SmsKey;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Repositories\SmsKeyRepository;
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
     * @var SmsKeyRepository
     */
    public $smsKeyRepository;

    /**
     * RegistrationService constructor.
     * @param UserRepository $userRepository
     * @param SmsKeyRepository $smsKeyRepository
     */
    public function __construct(UserRepository $userRepository, SmsKeyRepository $smsKeyRepository)
    {
        $this->userRepository = $userRepository;
        $this->smsKeyRepository = $smsKeyRepository;
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
            if ($key = $this->smsKeyRepository->createNewSmsKey($id, SmsKey::TYPE_REGISTRATION)) {
                DB::commit();

                return [
                    'status' => 'success',
                    'key' => $key,
                ];
            }
        }

        DB::rollBack();

        throw new RegistrationException(__('exception.server_error'), JsonRpcException::SERVER_ERROR);
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
        $user = $this->userRepository->findByPhone($params['phone'], [User::STATUS_NEW]);

        DB::beginTransaction();
        if ($user) {
            if ($key = $this->smsKeyRepository->checkKey($user->id, $params['key'], SmsKey::TYPE_REGISTRATION)) {
                $this->userRepository->confirmUser($user);
                $this->smsKeyRepository->changeUsedStatus($key);

                DB::commit();

                return ['status' => 'success'];
            }

            DB::rollBack();

            throw new RegistrationException(__('response.error_failed_sms_key'), JsonRpcException::FAILED_SMS_KEY);
        }

        throw new RegistrationException(__('response.user_not_found'), JsonRpcException::USER_NOT_FOUND);
    }
}
