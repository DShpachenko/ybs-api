<?php

namespace App\Modules\Auth\Requests;

use App\Modules\Auth\Models\User;
use Illuminate\Http\Request;
use App\Modules\Auth\Exceptions\ValidationException;
use App\Modules\Auth\Repositories\RefreshTokenRepository;
use Illuminate\Support\Facades\Validator;

/**
 * Валидация входящего запроса.
 *
 * Class Validation
 */
class Validation
{
    /**
     * Параметры запроса.
     *
     * @var array
     */
    public $params;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Список ошибок.
     *
     * @var array
     */
    public $fails;

    public $refreshTokenRepository;

    /**
     * Validation constructor.
     * @param Request $request
     * @param RefreshTokenRepository $refreshTokenRepository
     * @throws ValidationException
     */
    public function __construct(Request $request, RefreshTokenRepository $refreshTokenRepository)
    {
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->request = $request;
        $this->prepareData();
        $this->make();
    }

    /**
     * Подготовка данных.
     */
    public function prepareData(): void
    {
        $this->params = $this->request->json()->get('params');
    }

    /**
     * Запуск валидации.
     *
     * @throws ValidationException
     */
    public function make(): void
    {
        $this->beforeValidation();

        $rules = $this->rules();
        $messages = $this->messages();

        if (!$rules) {
            throw new ValidationException(__('errors.no_validation_rules'));
        }

        if (!$messages) {
            throw new ValidationException(__('errors.no_validation_messages'));
        }

        $validator = Validator::make($this->params, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors()->messages());
        }

        $this->afterValidation();
    }

    /**
     * Список правил валидации.
     *
     * @return array
     */
    protected function rules(): ? array
    {
        return null;
    }

    /**
     * Список сообщений для валидации запроса.
     *
     * @return array
     */
    protected function messages(): ? array
    {
        return null;
    }

    /**
     * Проверка актуальности токена.
     *
     * @param bool $refreshToken
     * @throws ValidationException
     */
    public function checkToken(bool $refreshToken = false): void
    {
        $token = $refreshToken ? $this->params['refresh_token'] : $this->params['access_token'];

        $payLoad = RefreshTokenRepository::tokenDecomposition($token);
        RefreshTokenRepository::tokenValidation($token, $payLoad);

        if ($refreshToken) {
            $tokenRow = $this->refreshTokenRepository->getToken($token, $payLoad['user_id']);

            if (!$tokenRow || $tokenRow->user->status !== User::STATUS_CONFIRMED) {
                throw new ValidationException(__('response.token_failed'));
            }

            $this->params['user_id'] = $payLoad['user_id'];
        }
    }

    /**
     * Возможность расширить список выполняемых проверок до прохождения валидации.
     */
    public function beforeValidation(): void
    {
        // any
    }

    /**
     * Возможность расширить список выполняемых проверок после прохождения валидации.
     */
    public function afterValidation(): void
    {
        // any
    }
}
