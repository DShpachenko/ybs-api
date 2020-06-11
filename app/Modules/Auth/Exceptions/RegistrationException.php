<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

/**
 * Exception для валидации регистрации.
 *
 * Class RegistrationException
 */
class RegistrationException extends Exception
{
    /**
     * RegistrationException constructor.
     * @param $message
     * @param $code
     */
    public function __construct($message, $code)
    {
        parent::__construct(json_encode($message), $code);
    }
}
