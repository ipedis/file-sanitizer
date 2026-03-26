<?php

declare(strict_types=1);

namespace Ipedis\FileSanitizer\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;

final class ScriptTagCleanupStep extends CleanupStepAbstract
{
    private const PATTERN = '#<script(.*?)>(.*?)</script>#is';


    protected function process(Payload $payload): Payload
    {
        $content = $payload->getContent();
        return $payload->setContent(
            (string) preg_replace(self::PATTERN, '', $content)
        );
    }
}
