<?php

declare(strict_types=1);

namespace Ipedis\FileSanitizer\Pipeline;

use Ipedis\FileSanitizer\Configuration\Configuration;
use Ipedis\FileSanitizer\Exception\InvalidCleanupStepException;
use Ipedis\FileSanitizer\Pipeline\Steps\CleanupStepAbstract;
use League\Pipeline\PipelineBuilder as BasePipelineBuilder;
use League\Pipeline\PipelineInterface;

abstract class PipelineSanitizerAbstract
{

    public function __construct(public readonly ?Configuration $configuration)
    {
    }

    /**
     * @throws InvalidCleanupStepException
     */
    final public function sanitize(string $content): Payload
    {
        $pipeline = $this->build();
        return $pipeline->process(Payload::build($content));
    }

    /**
     * @throws InvalidCleanupStepException
     */
    private function build(): PipelineInterface
    {
        $pipelineBuilder = new BasePipelineBuilder();
        foreach ([...$this->getRegisteredCleanupStep(), ...$this->configuration?->customSteps ?? []] as $cleanupStep) {
            if (in_array($cleanupStep, $this->configuration?->ignoredSteps ?? [])) {
                continue;
            }

            if (!is_subclass_of($cleanupStep, CleanupStepAbstract::class)) {
                throw new InvalidCleanupStepException(step: $cleanupStep);
            }

            $cleanupStep = new $cleanupStep();
            $pipelineBuilder->add($cleanupStep);
        }

        return $pipelineBuilder->build();
    }

    /**
     * ordered className list of cleanup.
     */
    protected abstract function getRegisteredCleanupStep(): array;

}
