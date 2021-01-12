<?php

namespace Mumble\MBurger\Exceptions;

use Exception;

class MBurgerUnauthorizedException extends Exception
{
    public static function create(string $message): self
    {
        return new static($message);
    }
}
