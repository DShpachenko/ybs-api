<?php

namespace App\Http\Response;

/**
 * Class JsonRpcResponse
 * @package App\Http\Response
 */
class JsonRpcResponse
{
    /**
     * JsonRpc version
     */
    public const JSON_RPC_VERSION = '2.0';

    /**
     * Success response
     *
     * @param $result
     * @param string|null $id
     * @return array
     */
    public static function success($result, string $id = null): ? array
    {
        return [
            'jsonrpc' => self::JSON_RPC_VERSION,
            'result' => $result,
            'id' => $id,
        ];
    }

    /**
     * Error response
     *
     * @param $error
     * @param $code
     * @return array
     */
    public static function error($error, $code): array
    {
        $error = isJson($error) ? json_decode($error, true) : $error;
        $error = is_string($error) ? ['message' => $error] : $error;

        if ($code === 0) {
            $error = ['message' => __('exception.server_error')];
        }

        return [
            'jsonrpc' => self::JSON_RPC_VERSION,
            'error' => array_merge(['code' => $code], $error),
            'id' => null,
        ];
    }
}
