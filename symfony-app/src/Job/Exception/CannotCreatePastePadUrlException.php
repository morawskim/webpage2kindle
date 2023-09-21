<?php

namespace App\Job\Exception;

final class CannotCreatePastePadUrlException extends \RuntimeException
{
    public static function wrongResponseCode(int $statusCode): self
    {
        return new static(
            sprintf('Cannot create PastePad URL. Expected 302 status code, got %d', $statusCode),
        );
    }
}
