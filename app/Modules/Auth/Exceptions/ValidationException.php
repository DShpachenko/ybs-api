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
     * @param null $message
     */
    public function __construct($message = null)
    {
        parent::__construct(json_encode($message));
    }
}
