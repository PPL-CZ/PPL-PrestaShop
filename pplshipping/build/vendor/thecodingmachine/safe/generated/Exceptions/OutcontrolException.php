<?php

namespace PPLShipping\Safe\Exceptions;

class OutcontrolException extends \ErrorException implements SafeExceptionInterface
{
    public static function createFromPhpError() : self
    {
        $error = \error_get_last();
        return new self($error['message'] ?? 'An error occured', 0, $error['type'] ?? 1);
    }
}
