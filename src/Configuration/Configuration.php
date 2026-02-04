<?php

declare(strict_types=1);

namespace Ipedis\FileSanitizer\Configuration;

use Ipedis\FileSanitizer\Exception\InvalidCleanupStepException;
use Ipedis\FileSanitizer\Pipeline\Steps\CleanupStepAbstract;

final class Configuration
{
    /**
     * @throws InvalidCleanupStepException
     */
    public function __construct(
        public readonly array $ignoredSteps = [],
        public readonly array $customSteps = []
    ) {
        $this->verify();
    }

    /**
     * @throws InvalidCleanupStepException
     */
    private function verify(): void
    {
        foreach ($this->customSteps as $customStep) {
            if (!is_subclass_of($customStep, CleanupStepAbstract::class)) {
                throw new InvalidCleanupStepException(step: $customStep);
            }
        }
    }
}
