<?php

declare(strict_types=1);

namespace App\Exception;

class ValidationException extends \Exception
{
    function __construct($message, $code = 400)
    {
        parent::__construct($message, $code);
    }
}
