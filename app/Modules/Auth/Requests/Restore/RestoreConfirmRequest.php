<?php

namespace App\Modules\Auth\Requests\Restore;

use App\Modules\Auth\Models\User;
use App\Modules\Auth\Requests\Validation;
use App\Modules\Auth\Repositories\UserRepository;
use Illuminate\Validation\Rule;

/**
 * Валидатор запроса подтверждения восстановления пароля.
 *
 * Class RestoreConfirmRequest
 * @package App\Modules\Auth\Requests\Registration
 */
class RestoreConfirmRequest extends Validation
{
    /**
     * Список правил валидации.
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|min:5|max:30|exists:users,phone',
            'password' => 'required|min:6|max:50',
            'code' => 'required|min:4|max:4',
        ];
    }

    /**
     * Список сообщений для валидации запроса.
     */
    public function messages(): array
    {
        return [
            'phone.required' => __('response.phone_required'),
            'code.required' => __('response.code_required'),
            'password.required' => __('response.password_required'),
            'min' => __('response.min'),
            'max' => __('response.max'),
            'exists' => __('response.user_not_found'),
        ];
    }

    /**
     * Чистим телефонный номер для дальнейшего поиска пользователя.
     */
    public function beforeValidation(): void
    {
        $this->params['phone'] = UserRepository::clearPhoneNumber($this->params['phone']);
    }
}
