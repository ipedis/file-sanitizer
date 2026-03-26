<?php

declare(strict_types=1);

namespace Ipedis\FileSanitizer\Configuration;

use Ipedis\FileSanitizer\Exception\InvalidCleanupStepException;
use Ipedis\FileSanitizer\Pipeline\Steps\CleanupStepAbstract;

final readonly class Configuration
{
    /**
     * @param array<class-string<CleanupStepAbstract>> $ignoredSteps
     * @param array<class-string<CleanupStepAbstract>> $customSteps
     *
     * @throws InvalidCleanupStepException
     */
    public function __construct(
        public array $ignoredSteps = [],
        public array $customSteps = []
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
                throw new InvalidCleanupStepException(step: (string) $customStep);
            }
        }
    }
}
