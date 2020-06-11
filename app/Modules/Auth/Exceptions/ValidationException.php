<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

/**
 * Exception для валидации запросов.
 *
 * Class ValidationException
 */
class ValidationException extends Exception
{
    /**
     * ValidationException constructor.
     * @param $message
     * @param $code
     */
    public function __construct($message, $code)
    {
        parent::__construct(json_encode($message), $code);
    }
}
