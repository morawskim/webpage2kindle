<?php

namespace App\Job\ReadablePageContent;

use App\Job\Exception\CannotCreateReadableVersionException;

interface CreateReadablePageContentInterface
{
    /**
     * @throws CannotCreateReadableVersionException
     */
    public function createReadableVersionOfWebPageContent(string $body, string $url): string;
}
