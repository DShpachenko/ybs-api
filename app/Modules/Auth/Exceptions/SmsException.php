<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

/**
 * Exception для валидации повторной отправки смс кода.
 *
 * Class SmsException
 */
class SmsException extends Exception
{
    /**
     * RestoreException constructor.
     * @param null $message
     */
    public function __construct($message = null)
    {
        parent::__construct(json_encode($message));
    }
}
