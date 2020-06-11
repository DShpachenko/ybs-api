<?php

namespace App\Exceptions;

use Exception;

/**
 * Class JsonRpcException
 * @package App\Exceptions
 */
class JsonRpcException extends Exception
{
    public const UNKNOWN_ERROR = 0;

    public const PARSE_ERROR = 1;
    public const INVALID_REQUEST = 2;
    public const METHOD_NOT_FOUND = 3;
    public const INVALID_PARAMS = 4;
    public const INTERNAL_ERROR = 5;

    public const SERVER_ERROR = 6;
    public const SERVER_ERROR_MIN = 7;
    public const SERVER_ERROR_MAX = 8;

    public const USER_NOT_FOUND = 9;
    public const FAILED_SMS_KEY = 10;
    public const TOKEN_FAILED = 11;
    public const TOKEN_INCORRECT = 12;
    public const EMPTY_VALIDATION_RULES = 13;
    public const EMPTY_VALIDATION_MESSAGES = 14;
    public const INVALID_LOGIN_PASS = 15;

    /** @var mixed|null */
    protected $_data;

    /**
     * @param int             $code
     * @param string|null     $message
     * @param mixed|null      $data
     * @param \Exception|null $previous
     */
    public function __construct(
        $code = self::UNKNOWN_ERROR,
        $message = null,
        $data = null,
        Exception $previous = null
    ) {
        if ($message === null) {
            $message = self::getErrorMessage($code);
        }

        $this->_data = $data;

        parent::__construct($message, $code);
    }

    /**
     * @return array|string[]
     */
    protected static function getErrorMessages(): array
    {
        return [
            self::UNKNOWN_ERROR => __('exception.unknown_error'),
            self::PARSE_ERROR => __('exception.parse_error'),
            self::INVALID_REQUEST => __('exception.invalid_request'),
            self::METHOD_NOT_FOUND => __('exception.method_not_found'),
            self::INVALID_PARAMS => __('exception.invalid_params'),
            self::INTERNAL_ERROR => __('exception.internal_error'),
            self::SERVER_ERROR => __('exception.server_error'),
        ];
    }

    /**
     * Return error message from error code.
     *
     * @param $errorCode
     * @return mixed|string|null
     */
    public static function getErrorMessage($errorCode)
    {
        $errorMessages = static::getErrorMessages();

        if (isset($errorMessages[$errorCode])) {
            return $errorMessages[$errorCode];
        }

        if ($errorCode >= self::SERVER_ERROR_MIN
            && $errorCode <= self::SERVER_ERROR_MAX) {

            return $errorMessages[self::SERVER_ERROR];
        }

        return null;
    }

    /**
     * Return exception data.
     *
     * @return mixed|null
     */
    public function getData()
    {
        return $this->_data;
    }
}
