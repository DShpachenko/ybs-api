<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

/**
 * Exception для валидации авторизации.
 *
 * Class AuthException
 */
class AuthException extends Exception
{
    /**
     * AuthException constructor.
     * @param null $message
     */
    public function __construct($message = null)
    {
        parent::__construct(json_encode($message));
    }
}
