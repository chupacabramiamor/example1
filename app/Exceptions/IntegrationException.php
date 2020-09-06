<?php

namespace App\Exceptions;

use Exception;

class IntegrationException extends Exception
{
    public function __construct($message = 'undefined_error', $code = 0, Exception $previous = null)
    {
        parent::__construct(__($message), $code, $previous);
    }
}
