<?php

namespace App\Job\Exception;

final class CannotGetPageContentException extends \RuntimeException
{
    public static function default(string $message, \Throwable $previous): self
    {
        return new static(
            sprintf('Cannot fetch page content. Reason: %s', $message),
            previous: $previous
        );
    }
}
