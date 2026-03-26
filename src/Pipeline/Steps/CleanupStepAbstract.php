<?php

declare(strict_types=1);

namespace Ipedis\FileSanitizer\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;

abstract class CleanupStepAbstract
{
    final public function __invoke(Payload $payload): Payload
    {
        return $this->process($payload);
    }

    abstract protected function process(Payload $payload): Payload;
}
