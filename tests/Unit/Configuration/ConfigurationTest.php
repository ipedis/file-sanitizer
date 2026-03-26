<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Configuration;

use Ipedis\FileSanitizer\Configuration\Configuration;
use Ipedis\FileSanitizer\Exception\InvalidCleanupStepException;
use Ipedis\FileSanitizer\Pipeline\Steps\CdataTagCleanupStep;
use Ipedis\Tests\Unit\Configuration\Steps\InvalidCustomStep;
use Ipedis\Tests\Unit\Configuration\Steps\ValidCustomStep;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ConfigurationTest extends TestCase
{
    /**
     */
    #[Test]
    public function with_empty_data(): void
    {
        $configuration = new Configuration();
        $this->assertEmpty($configuration->customSteps);
        $this->assertEmpty($configuration->ignoredSteps);
    }

    /**
     */
    #[Test]
    public function with_valid_data(): void
    {
        $configuration = new Configuration(
            ignoredSteps: [CdataTagCleanupStep::class],
            customSteps: [ValidCustomStep::class]
        );

        $this->assertNotEmpty($configuration->ignoredSteps);
        $this->assertNotEmpty($configuration->customSteps);
    }

    /**
     */
    #[Test]
    public function with_invalid_data(): void
    {
        $this->expectException(InvalidCleanupStepException::class);
        new Configuration(
            customSteps: [InvalidCustomStep::class] // @phpstan-ignore argument.type
        );
    }

}
