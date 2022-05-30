<?php

namespace Ipedis\FileSanitizer\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;

final class CdataTagCleanupStep extends CleanupStepAbstract
{
    private const PATTERN = '#<!\[CDATA(.*?)>(.*?)]]>([\s\S]*?)<!\[CDATA(.*?)>(.*?)]]>#i';

    protected function process(Payload $payload): Payload
    {
        $content = $payload->getContent();

        return $payload->setContent(preg_replace(self::PATTERN, '', $content));
    }
}
