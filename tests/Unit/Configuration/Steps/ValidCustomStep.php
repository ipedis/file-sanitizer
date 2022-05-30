<?php

namespace Ipedis\Tests\Unit\Configuration\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\CleanupStepAbstract;

final class ValidCustomStep extends CleanupStepAbstract
{
    protected function process(Payload $payload): Payload
    {
        return $payload;
    }


}
