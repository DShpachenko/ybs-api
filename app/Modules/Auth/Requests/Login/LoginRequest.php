<?php

namespace App\Modules\Auth\Requests\Login;

use App\Modules\Auth\Models\User;
use App\Modules\Auth\Requests\Validation;
use App\Modules\Auth\Repositories\UserRepository;
use Illuminate\Validation\Rule;

/**
 * Валидатор запроса авторизации.
 *
 * Class LoginRequest
 * @package App\Modules\Auth\Requests\Login
 */
class LoginRequest extends Validation
{
    /**
     * Список правил валидации.
     */
    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'min:5',
                'max:30',
                Rule::exists('users')->where(static function ($query) {
                    $query->where('status', User::STATUS_CONFIRMED);
                })
            ],
            'password' => 'required|min:6|max:50',
        ];
    }

    /**
     * Список сообщений для валидации запроса.
     */
    public function messages(): array
    {
        return [
            'phone.required' => __('response.phone_required'),
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
