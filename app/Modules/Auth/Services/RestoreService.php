<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Exceptions\RestoreException;
use App\Modules\Auth\Models\SmsCode;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Repositories\RefreshTokenRepository;
use App\Modules\Auth\Repositories\SmsCodeRepository;
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
     * @var SmsCodeRepository
     */
    public $smsCodeRepository;

    /**
     * @var RefreshTokenRepository
     */
    public $tokenRepository;

    /**
     * RestoreService constructor.
     * @param UserRepository $userRepository
     * @param SmsCodeRepository $smsCodeRepository
     * @param RefreshTokenRepository $tokenRepository
     */
    public function __construct(UserRepository $userRepository, SmsCodeRepository $smsCodeRepository, RefreshTokenRepository $tokenRepository)
    {
        $this->userRepository = $userRepository;
        $this->smsCodeRepository = $smsCodeRepository;
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

            if ($code = $this->smsCodeRepository->createNewSmsCode($user->id, SmsCode::TYPE_PASSWORD_RECOVERY)) {
                DB::commit();

                return [
                    'status' => 'success',
                    'code' => $code,
                ];
            }

            DB::rollBack();

            throw new RestoreException(__('exception.server_error'));
        }

        throw new RestoreException(__('response.user_not_found'));
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
            if ($code = $this->smsCodeRepository->checkCode($user->id, $params['code'], SmsCode::TYPE_PASSWORD_RECOVERY)) {
                DB::beginTransaction();

                try {
                    switch ($user->status) {
                        case User::STATUS_NEW: {
                            $this->userRepository->confirmUser($user);
                            $this->userRepository->updatePassword($user, $params['password']);
                            $this->smsCodeRepository->changeUsedStatus($code);
                        }
                        case User::STATUS_CONFIRMED: {
                            $this->userRepository->updatePassword($user, $params['password']);
                            $this->smsCodeRepository->changeUsedStatus($code);
                            $this->tokenRepository->disableAllUserTokens($user->id);
                        }
                    }

                    DB::commit();

                    return ['status' => 'success'];
                } catch (\Exception $e) {
                    DB::rollBack();

                    throw new RestoreException(__('exception.server_error'));
                }
            }

            throw new RestoreException(__('response.error_failed_sms_code'));
        }

        throw new RestoreException(__('response.user_not_found'));
    }
}
