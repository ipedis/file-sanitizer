<?php

declare(strict_types=1);

namespace Ipedis\Tests\Data;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\CleanupStepAbstract;

class CustomCleanupStepTest extends CleanupStepAbstract
{
    protected function process(Payload $payload): Payload
    {
        return $payload->setContent('new custom step');
    }


}
