<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Configuration;

use Ipedis\FileSanitizer\Configuration\Configuration;
use Ipedis\FileSanitizer\Exception\InvalidCleanupStepException;
use Ipedis\FileSanitizer\Pipeline\Steps\CdataTagCleanupStep;
use Ipedis\Tests\Unit\Configuration\Steps\InvalidCustomStep;
use Ipedis\Tests\Unit\Configuration\Steps\ValidCustomStep;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    public function testWithEmptyData(): void
    {
        $configuration = new Configuration();
        $this->assertEmpty($configuration->customSteps);
        $this->assertEmpty($configuration->ignoredSteps);
    }

    public function testWithValidData(): void
    {
        $configuration = new Configuration(
            ignoredSteps: [CdataTagCleanupStep::class],
            customSteps: [ValidCustomStep::class]
        );

        $this->assertNotEmpty($configuration->ignoredSteps);
        $this->assertNotEmpty($configuration->customSteps);
    }

    public function testWithInvalidData(): void
    {
        $this->expectException(InvalidCleanupStepException::class);
        new Configuration(
            customSteps: [InvalidCustomStep::class]
        );
    }

}
