<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\AttributeCleanupStep;
use PHPUnit\Framework\TestCase;

final class AttributeCleanupStepTest extends TestCase
{
    public function testProcess(): void
    {
        $payload = Payload::build(
            content: '<img src="jav&#x09;ascript:alert(\'hacked\');">'
        );
        $attributeCleanupStep = new AttributeCleanupStep();
        $payload = $attributeCleanupStep($payload);
        $this->assertStringContainsString('src=""', $payload->getContent());
    }
}
