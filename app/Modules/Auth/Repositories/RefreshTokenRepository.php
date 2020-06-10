<?php

namespace App\Modules\Auth\Repositories;

use App\Modules\Auth\Exceptions\ValidationException;
use Carbon\Carbon;
use App\Repositories\Repository;
use App\Modules\Auth\Models\RefreshTokens;

/**
 * Class RefreshTokenRepository
 * @package App\Modules\Auth\Repositories
 */
class RefreshTokenRepository extends Repository
{
    /**
     * @return mixed|string
     */
    public function model()
    {
        return RefreshTokens::class;
    }

    /**
     * @param $userId
     * @return array|null
     */
    public function add($userId): ? array
    {
        $accessToken = $this->generateAccessToken($userId);
        $refreshToken = $this->generateRefreshToken($userId);

        if ($this->create([
            'user_id' => $userId,
            'status' => RefreshTokens::STATUS_ACTIVE,
            'token' => $refreshToken,
        ])) {
            return [
                'refresh_token' => $refreshToken,
                'access_token' => $accessToken,
            ];
        }
        return null;
    }

    /**
     * @param $userId
     * @return string
     */
    private function generateAccessToken($userId): string
    {
        return self::generateJwt([
            'user_id' => $userId,
            'life_time' => RefreshTokens::ACCESS_TOKEN_LIFE_TIME,
            'end_point' => Carbon::now()->addSeconds(RefreshTokens::ACCESS_TOKEN_LIFE_TIME),
        ]);
    }

    /**
     * @param $userId
     * @return string
     */
    private function generateRefreshToken($userId): string
    {
        return self::generateJwt([
            'user_id' => $userId,
            'life_time' => RefreshTokens::REFRESH_TOKEN_LIFE_TIME,
            'end_point' => Carbon::now()->addSeconds(RefreshTokens::REFRESH_TOKEN_LIFE_TIME),
        ]);
    }

    /**
     * @param $payload
     * @return string
     */
    public static function generateJwt($payload): string
    {
        // Создание заголовка (header) в формате JSON строки.
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        // Создание payload.
        $payload = json_encode($payload);

        // Получение Header в формате строки вида Base64Url.
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        // Получение Payload в формате строки вида Base64Url.
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // Создание сигнатуры.
        $signature = hash_hmac('sha256', $base64UrlHeader.".".$base64UrlPayload, env('SECRET_KEY'), true);

        // Получение Signature в формате строки вида Base64Url.
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader.'.'.$base64UrlPayload.'.'.$base64UrlSignature;
    }

    /**
     * Разбивка токена.
     *
     * @param $token
     * @return array|null
     * @throws ValidationException
     */
    public static function tokenDecomposition($token): ? array
    {
        try {
            [$header, $payload, $signature] = explode('.', $token);

            $payloadJson = base64_decode($payload);

            return json_decode($payloadJson, true);
        } catch (\Exception $e) {
            throw new ValidationException(__('exception.server_error'));
        }
    }

    /**
     * Валидация токена.
     *
     * @param $token
     * @param $payload
     * @throws ValidationException
     */
    public static function tokenValidation($token, $payload): void
    {
        try {
            $comparisonToken = self::generateJwt($payload);

            if ($token === $comparisonToken) {
                if ($payload['end_point'] < Carbon::now()) {
                    throw new ValidationException(__('response.token_failed'));
                }
            } else {
                throw new ValidationException(__('response.token_incorrect'));
            }
        } catch (\Exception $e) {
            throw new ValidationException(__('response.token_incorrect'));
        }
    }

    /**
     * Получение токена.
     *
     * @param $token
     * @param $userId
     * @return mixed
     */
    public function getToken($token, $userId) : ? RefreshTokens
    {
        return $this->model
            ->where('token', $token)
            ->where('user_id', $userId)
            ->where('status', RefreshTokens::STATUS_ACTIVE)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /**
     * Отключение токена.
     *
     * @param $token
     */
    public function disableToken($token): void
    {
        $this->model
            ->where('token', $token)
            ->update(['status' => RefreshTokens::STATUS_DISABLED]);
    }

    /**
     * Отключение всех токенов у пользователя.
     *
     * @param $userId
     */
    public function disableAllUserTokens($userId): void
    {
        $this->model
            ->where('user_id', $userId)
            ->update(['status' => RefreshTokens::STATUS_DISABLED]);
    }
}
