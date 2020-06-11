<?php

namespace App\Modules\Auth\Requests\Registration;

use App\Modules\Auth\Models\User;
use App\Modules\Auth\Requests\Validation;
use App\Modules\Auth\Repositories\UserRepository;
use Illuminate\Validation\Rule;

/**
 * Валидатор запроса подтверждения регистрации нового пользователя.
 *
 * Class RegistrationConfirmRequest
 * @package App\Modules\Auth\Requests\Registration
 */
class RegistrationConfirmRequest extends Validation
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
                    $query->where('status', User::STATUS_NEW);
                })
            ],
            'key' => 'required|min:4|max:4',
        ];
    }

    /**
     * Список сообщений для валидации запроса.
     */
    public function messages(): array
    {
        return [
            'phone.required' => __('response.phone_required'),
            'key.required' => __('response.key_required'),
            'min' => __('response.min'),
            'max' => __('response.max'),
            'exists' => __('response.user_not_found'),
            'unique' => __('response.phone_unique'),
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
