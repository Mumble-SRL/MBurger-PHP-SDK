<?php

namespace Mumble\MBurger\Exceptions;

use Exception;

class MBurgerNotFoundException extends Exception
{
    public static function create(string $message): self
    {
        return new static($message);
    }
}
