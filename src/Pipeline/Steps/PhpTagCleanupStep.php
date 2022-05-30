<?php

namespace Ipedis\FileSanitizer\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;

final class PhpTagCleanupStep extends CleanupStepAbstract
{
    protected function process(Payload $payload): Payload
    {
        $content = $payload->getContent();
        $content = str_replace("<?php", '', $content);
        $content = str_replace("<?", '', $content);
        $content = str_replace("?>", '', $content);

        return $payload->setContent($content);
    }
}
