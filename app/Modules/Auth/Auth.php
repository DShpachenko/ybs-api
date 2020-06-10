<?php

namespace App\Modules\Auth;

use App\Modules\Auth\Requests\Login\LoginRequest;
use App\Modules\Auth\Requests\Login\LogoutRequest;
use App\Modules\Auth\Requests\Restore\RestoreConfirmRequest;
use App\Modules\Auth\Requests\Restore\RestoreStartRequest;
use App\Modules\Auth\Requests\Sms\SendSmsRequest;
use App\Modules\Auth\Requests\Token\RefreshTokenRequest;
use App\Modules\Auth\Requests\Registration\RegistrationStartRequest;
use App\Modules\Auth\Requests\Registration\RegistrationConfirmRequest;
use App\Modules\Auth\Services\RestoreService;
use App\Modules\Auth\Services\SmsService;
use App\Modules\Auth\Services\TokenService;
use App\Modules\Auth\Services\LoginService;
use App\Modules\Auth\Services\RegistrationService;

/**
 * Trait Auth
 * @package App\Modules\Auth
 */
trait Auth
{
    /**
     * Начало регистрации до подтверждения телефона.
     *
     * @param RegistrationStartRequest $request
     * @param RegistrationService $service
     * @return array|null
     * @throws Exceptions\RegistrationException
     */
    public function registrationStart(RegistrationStartRequest $request, RegistrationService $service): ? array
    {
        return $service->registrationStart($request->params);
    }

    /**
     * Подтверждение регистрации.
     *
     * @param RegistrationConfirmRequest $request
     * @param RegistrationService $service
     * @return array|null
     * @throws Exceptions\RegistrationException
     */
    public function registrationConfirm(RegistrationConfirmRequest $request, RegistrationService $service): ? array
    {
        return $service->registrationConfirm($request->params);
    }

    /**
     * Повторная отправка смс-кода.
     *
     * @param SendSmsRequest $request
     * @param SmsService $service
     * @return array|null
     * @throws Exceptions\SmsException
     */
    public function sendSms(SendSmsRequest $request, SmsService $service): ? array
    {
        return $service->sendSms($request->params);
    }

    /**
     * Авторизация
     *
     * @param LoginRequest $request
     * @param LoginService $service
     * @return array|null
     * @throws Exceptions\AuthException
     */
    public function login(LoginRequest $request, LoginService $service): ? array
    {
        return $service->login($request->params);
    }

    /**
     * Logout и сброс указанного refresh_token.
     *
     * @param LogoutRequest $request
     * @param LoginService $service
     * @return array|null
     */
    public function logout(LogoutRequest $request, LoginService $service): ? array
    {
        return $service->logout($request->params);
    }

    /**
     * Начало восстановления пароля.
     *
     * @param RestoreStartRequest $request
     * @param RestoreService $service
     * @return array|null
     * @throws Exceptions\RestoreException
     */
    public function restoreStart(RestoreStartRequest $request, RestoreService $service)
    {
        return $service->restoreStart($request->params);
    }

    /**
     * @param RestoreConfirmRequest $request
     * @param RestoreService $service
     * @return array|null
     * @throws Exceptions\RestoreException
     */
    public function restoreConfirm(RestoreConfirmRequest $request, RestoreService $service): ? array
    {
        return $service->restoreConfirm($request->params);
    }

    /**
     * Обновление токена.
     *
     * @param RefreshTokenRequest $request
     * @param TokenService $service
     * @return array|null
     * @throws Exceptions\ValidationException
     */
    public function updateToken(RefreshTokenRequest $request, TokenService $service): ? array
    {
        return $service->refresh($request->params);
    }
}
