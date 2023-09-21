<?php

namespace App\Job;

class JobUtils
{
    private const SEPARATOR = "\n\n";

    /**
     * @return string[]
     */
    public static function extractWebPageContentAndTitle(string $jobWebContent): array
    {
        return explode(self::SEPARATOR, $jobWebContent, 2);
    }

    public static function joinTitleAndWebContent(string $title, string $webPageContent): string
    {
        return $title . self::SEPARATOR . $webPageContent;
    }
}
