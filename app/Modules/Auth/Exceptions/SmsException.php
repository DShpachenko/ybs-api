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
     * @param $message
     * @param $code
     */
    public function __construct($message, $code)
    {
        parent::__construct(json_encode($message), $code);
    }
}
