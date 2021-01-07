<?php

namespace Mumble\MBurger\Exceptions;

use Exception;

class MBurgerServerErrorException extends Exception
{
    public static function create(string $message): self
    {
        return new static($message);
    }
}
