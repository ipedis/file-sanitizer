<?php

declare(strict_types=1);

namespace Ipedis\FileSanitizer\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;

final class DecodeTagCleanupStep extends CleanupStepAbstract
{
    protected function process(Payload $payload): Payload
    {
        // transform html tag encrypted , ex : '<' => '&lt;'
        return $payload->setContent(htmlspecialchars_decode($payload->getContent()));
    }
}
