<?php

namespace App\Exceptions;

use Exception;

/**
 * Class JsonRpcException
 * @package App\Exceptions
 */
class RepositoryException extends Exception
{
    /**
     * JsonRpcException constructor.
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
