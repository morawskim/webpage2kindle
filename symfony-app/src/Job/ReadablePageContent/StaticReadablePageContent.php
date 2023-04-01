<?php

namespace App\Job\ReadablePageContent;

class StaticReadablePageContent implements CreateReadablePageContentInterface
{
    public function createReadableVersionOfWebPageContent(string $body, string $url, string $title): string
    {
        return strip_tags($body);
    }
}
