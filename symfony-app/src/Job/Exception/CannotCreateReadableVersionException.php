<?php

namespace App\Job\Exception;

class CannotCreateReadableVersionException extends \RuntimeException
{
    public static function default(\Throwable $previous): self
    {
        return new static('Cannot create readable version.', previous: $previous);
    }
}
