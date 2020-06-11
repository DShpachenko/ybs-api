<?php

namespace App\Modules\Auth\Services;

use App\Exceptions\JsonRpcException;
use App\Modules\Auth\Exceptions\SmsException;
use App\Modules\Auth\Models\SmsKey;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Repositories\SmsKeyRepository;
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
     * @var SmsKeyRepository
     */
    public $smsKeyRepository;

    /**
     * @var UserRepository
     */
    public $userRepository;

    /**
     * SmsService constructor.
     * @param UserRepository $userRepository
     * @param SmsKeyRepository $smsKeyRepository
     */
    public function __construct(UserRepository $userRepository, SmsKeyRepository $smsKeyRepository)
    {
        $this->smsKeyRepository = $smsKeyRepository;
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

            $type = SmsKey::TYPE_PASSWORD_RECOVERY;

            if ($user->status === User::STATUS_NEW) {
                $type = SmsKey::TYPE_REGISTRATION;
            }

            if ($key = $this->smsKeyRepository->createNewSmsKey($user->id, $type)) {
                DB::commit();

                return [
                    'status' => 'success',
                    'key' => $key,
                ];
            }

            DB::rollBack();

            throw new SmsException(__('exception.server_error'), JsonRpcException::SERVER_ERROR);
        }

        throw new SmsException(__('response.user_not_found'), JsonRpcException::USER_NOT_FOUND);
    }
}
