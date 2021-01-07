<?php

namespace Mumble\MBurger\Exceptions;

use Exception;

class MBurgerUnauthenticatedException extends Exception
{
    public static function create(string $message): self
    {
        return new static($message);
    }
}
