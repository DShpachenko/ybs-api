<?php

namespace App\Modules\Auth\Requests\Token;

use App\Modules\Auth\Requests\Validation;

/**
 * Валидатор запроса обновления токена.
 *
 * Class LogoutRequest
 * @package App\Modules\Auth\Requests\Login
 */
class RefreshTokenRequest extends Validation
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
