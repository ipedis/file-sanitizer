<?php

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

    final public function sanitize(string $content): Payload
    {
        $pipeline = $this->build();
        /** @var Payload $payload */
        return $pipeline->process(Payload::build($content));
    }

    private function build(): PipelineInterface
    {
        $pipeBuilder = new BasePipelineBuilder();
        foreach ([...$this->getRegisteredCleanupStep(), ...$this->configuration?->customSteps ?? []] as $cleanupStep) {
            if (in_array($cleanupStep, $this->configuration?->ignoredSteps ?? [])) {
                continue;
            }
            if (!is_subclass_of($cleanupStep, CleanupStepAbstract::class)) {
                throw new InvalidCleanupStepException(step: $cleanupStep);
            }
            $cleanupStep = new $cleanupStep();
            $pipeBuilder->add($cleanupStep);
        }

        return $pipeBuilder->build();
    }

    /**
     * ordered className list of cleanup.
     */
    protected abstract function getRegisteredCleanupStep(): array;

}
