<?php

namespace App\Modules\Auth\Requests\Registration;

use App\Modules\Auth\Models\User;
use App\Modules\Auth\Requests\Validation;
use App\Modules\Auth\Repositories\UserRepository;
use Illuminate\Validation\Rule;

/**
 * Валидатор запроса добавления нового пользователя.
 *
 * Class RegistrationStartRequest
 * @package App\Modules\Auth\Requests\Registration
 */
class RegistrationStartRequest extends Validation
{
    /**
     * Список правил валидации.
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|min:5|max:30|unique:users,phone',
            'name' => 'required|min:5|max:50',
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
            'name.required' => __('response.name_required'),
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
