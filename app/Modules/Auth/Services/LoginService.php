<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Exceptions\AuthException;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Repositories\RefreshTokenRepository;
use App\Modules\Auth\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Сервис авторизации.
 *
 * Class LoginService
 * @package App\Modules\Auth\Services
 */
class LoginService
{
    /**
     * @var UserRepository
     */
    public $userRepository;

    /**
     * @var RefreshTokenRepository
     */
    public $refreshTokenRepository;

    /**
     * LoginService constructor.
     * @param UserRepository $userRepository
     * @param RefreshTokenRepository $refreshTokenRepository
     */
    public function __construct(UserRepository $userRepository, RefreshTokenRepository $refreshTokenRepository)
    {
        $this->userRepository = $userRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    /**
     * Авторизация
     *
     * @param $params
     * @return array|null
     * @throws AuthException
     */
    public function login($params): ? array
    {
        $user = $this->userRepository->findByPhone($params['phone'], [User::STATUS_CONFIRMED]);

        if ($user && Hash::check($params['password'], $user->password)) {
            DB::beginTransaction();

            if ($token = $this->refreshTokenRepository->add($user->id)) {
                DB::commit();

                return $token;
            }

            DB::rollBack();
        }

        throw new AuthException(__('response.user_not_found'));
    }

    /**
     * Отключение токена.
     *
     * @param $params
     * @return array|string[]|null
     */
    public function logout($params): ? array
    {
        $this->refreshTokenRepository->disableToken($params['refresh_token']);

        return ['status' => 'OK'];
    }
}
