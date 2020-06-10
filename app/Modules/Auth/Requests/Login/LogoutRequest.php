<?php

namespace App\Modules\Auth\Requests\Login;

use App\Modules\Auth\Requests\Validation;

/**
 * Валидатор запроса сброса токена.
 *
 * Class LogoutRequest
 * @package App\Modules\Auth\Requests\Login
 */
class LogoutRequest extends Validation
{
    /**
     * Список правил валидации.
     */
    public function rules(): array
    {
        return [
            'refresh_token' => 'required',
        ];
    }

    /**
     * Список сообщений для валидации запроса.
     */
    public function messages(): array
    {
        return [
            'refresh_token.required' => __('response.refresh_token_required'),
        ];
    }

    /**
     * Проверка токена.
     *
     * @throws \App\Modules\Auth\Exceptions\ValidationException
     */
    public function afterValidation(): void
    {
        $this->checkToken(true);
    }
}
