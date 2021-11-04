<?php

namespace Mumble\MBurger\Exceptions;

use Exception;

class MBurgerInvalidRequestException extends Exception
{
    public static function create(string $message, int $code): self
    {
        return new static($message, $code);
    }
}
