<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Exceptions\ValidationException;
use App\Modules\Auth\Repositories\RefreshTokenRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class TokenService
 * @package App\Modules\Auth\Services
 */
class TokenService
{
    /**
     * @var RefreshTokenRepository
     */
    public $refreshTokenRepository;

    /**
     * TokenService constructor.
     * @param RefreshTokenRepository $refreshTokenRepository
     */
    public function __construct(RefreshTokenRepository $refreshTokenRepository)
    {
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    /**
     * Обновление токена
     *
     * @param $params
     * @return array|null
     * @throws ValidationException
     */
    public function refresh($params): ? array
    {
        DB::beginTransaction();
        $this->refreshTokenRepository->disableToken($params['refresh_token']);

        if ($token = $this->refreshTokenRepository->add($params['user_id'])) {
            DB::commit();

            return $token;
        }

        DB::rollBack();

        throw new ValidationException(__('response.token_failed'));
    }
}
