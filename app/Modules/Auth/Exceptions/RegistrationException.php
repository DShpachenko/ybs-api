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
     * @param null $message
     */
    public function __construct($message = null)
    {
        parent::__construct(json_encode($message));
    }
}
