<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

/**
 * Exception для валидации восстановления пароля.
 *
 * Class RestoreException
 */
class RestoreException extends Exception
{
    /**
     * RestoreException constructor.
     * @param $message
     * @param $code
     */
    public function __construct($message, $code)
    {
        parent::__construct(json_encode($message), $code);
    }
}
