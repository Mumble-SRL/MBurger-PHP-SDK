<?php

namespace Mumble\MBurger\Exceptions;

use Exception;

class MBurgeInvalidRequestException extends Exception
{
    public static function create(string $message): self
    {
        return new static($message);
    }
}
