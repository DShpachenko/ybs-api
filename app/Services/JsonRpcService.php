<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Response\JsonRpcResponse;
use App\Exceptions\JsonRpcException;

/**
 * Class JsonRpcService
 * @package App\Services
 */
class JsonRpcService
{
    public const JSON_RPC_VERSION = 2.0;

    /**
     * @param Request $request
     * @param Controller $controller
     * @return array|null
     */
    public function handle(Request $request, Controller $controller): ? array
    {
        try {
            $content = json_decode($request->getContent(), true);

            if (empty($content)) {
                throw new JsonRpcException(JsonRpcException::PARSE_ERROR);
            } else if (!isset($content['method'], $content['params'])) {
                throw new JsonRpcException(JsonRpcException::INVALID_REQUEST);
            }

            if ($content['jsonrpc'] !== self::JSON_RPC_VERSION) {
                throw new JsonRpcException(JsonRpcException::INVALID_JSONRPC_VERSION);
            }

            if (!method_exists($controller, $content['method'])) {
                throw new JsonRpcException(JsonRpcException::METHOD_NOT_FOUND);
            }

            $result = app()->call('App\Http\Controllers\Controller@'.$content['method'], $content['params']);

            return JsonRpcResponse::success($result, $content['id']);
        } catch (\Exception $e) {
            return JsonRpcResponse::error($e->getMessage(), $e->getCode());
        } catch (\Throwable $e) {
            return JsonRpcResponse::error(__('exception.server_error'), $e->getCode());
        }
    }
}
