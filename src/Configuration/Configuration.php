<?php

namespace Ipedis\FileSanitizer\Configuration;

use Ipedis\FileSanitizer\Exception\InvalidCleanupStepException;
use Ipedis\FileSanitizer\Pipeline\Steps\CleanupStepAbstract;

final class Configuration
{
    public function __construct(
        public readonly array $ignoredSteps = [],
        public readonly array $customSteps = []
    ) {
        $this->verify();
    }

    private function verify(): void
    {
        foreach ($this->customSteps as $step) {
            if (!is_subclass_of($step, CleanupStepAbstract::class)) {
                throw new InvalidCleanupStepException(step: $step);
            }
        }
    }
}
