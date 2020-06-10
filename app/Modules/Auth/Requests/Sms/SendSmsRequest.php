<?php

namespace App\Modules\Auth\Requests\Sms;

use App\Modules\Auth\Models\User;
use App\Modules\Auth\Repositories\UserRepository;
use App\Modules\Auth\Requests\Validation;
use Illuminate\Validation\Rule;

/**
 * Валидатор запроса повторной отправки смс кода.
 *
 * Class SendSmsRequest
 * @package App\Modules\Auth\Requests\Sms
 */
class SendSmsRequest extends Validation
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
                    $query->whereIn('status', [User::STATUS_CONFIRMED, User::STATUS_NEW]);
                })
            ],
        ];
    }

    /**
     * Список сообщений для валидации запроса.
     */
    public function messages(): array
    {
        return [
            'phone.required' => __('response.phone_required'),
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
