<?php

namespace App\Modules\Auth\Services;

use App\Exceptions\JsonRpcException;
use App\Modules\Auth\Exceptions\RestoreException;
use App\Modules\Auth\Models\SmsKey;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Repositories\RefreshTokenRepository;
use App\Modules\Auth\Repositories\SmsKeyRepository;
use App\Modules\Auth\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

/**
 * Сервис восстановления пароля.
 *
 * Class RestoreService
 * @package App\Modules\Auth\Services
 */
class RestoreService
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
     * @var RefreshTokenRepository
     */
    public $tokenRepository;

    /**
     * RestoreService constructor.
     * @param UserRepository $userRepository
     * @param SmsKeyRepository $smsKeyRepository
     * @param RefreshTokenRepository $tokenRepository
     */
    public function __construct(UserRepository $userRepository, SmsKeyRepository $smsKeyRepository, RefreshTokenRepository $tokenRepository)
    {
        $this->userRepository = $userRepository;
        $this->smsKeyRepository = $smsKeyRepository;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * Запуск восстановления пароля, отправка проверочного кода.
     *
     * @param $params
     * @return array|null
     * @throws RestoreException
     */
    public function restoreStart($params): ? array
    {
        if ($user = $this->userRepository->findByPhone($params['phone'], [User::STATUS_NEW, User::STATUS_CONFIRMED])) {
            DB::beginTransaction();

            if ($key = $this->smsKeyRepository->createNewSmsKey($user->id, SmsKey::TYPE_PASSWORD_RECOVERY)) {
                DB::commit();

                return [
                    'status' => 'success',
                    'key' => $key,
                ];
            }

            DB::rollBack();

            throw new RestoreException(__('exception.server_error'), JsonRpcException::SERVER_ERROR);
        }

        throw new RestoreException(__('response.user_not_found'), JsonRpcException::USER_NOT_FOUND);
    }

    /**
     * Подтверждение восстановления пароля.
     *
     * @param $params
     * @return array|string[]|null
     * @throws RestoreException
     */
    public function restoreConfirm($params): ? array
    {
        if ($user = $this->userRepository->findByPhone($params['phone'], [User::STATUS_NEW, User::STATUS_CONFIRMED])) {
            if ($key = $this->smsKeyRepository->checkKey($user->id, $params['key'], SmsKey::TYPE_PASSWORD_RECOVERY)) {
                DB::beginTransaction();

                try {
                    switch ($user->status) {
                        case User::STATUS_NEW: {
                            $this->userRepository->confirmUser($user);
                            $this->userRepository->updatePassword($user, $params['password']);
                            $this->smsKeyRepository->changeUsedStatus($key);
                        }
                        case User::STATUS_CONFIRMED: {
                            $this->userRepository->updatePassword($user, $params['password']);
                            $this->smsKeyRepository->changeUsedStatus($key);
                            $this->tokenRepository->disableAllUserTokens($user->id);
                        }
                    }

                    DB::commit();

                    return ['status' => 'success'];
                } catch (\Exception $e) {
                    DB::rollBack();

                    throw new RestoreException(__('exception.server_error'), JsonRpcException::SERVER_ERROR);
                }
            }

            throw new RestoreException(__('response.error_failed_sms_key'), JsonRpcException::FAILED_SMS_KEY);
        }

        throw new RestoreException(__('response.user_not_found'), JsonRpcException::USER_NOT_FOUND);
    }
}
