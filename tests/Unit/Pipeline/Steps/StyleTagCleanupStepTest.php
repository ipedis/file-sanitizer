<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\StyleTagCleanupStep;
use PHPUnit\Framework\TestCase;

final class StyleTagCleanupStepTest extends TestCase
{
    public function testProcess(): void
    {
        $payload = Payload::build(
            content: '<style>li {list-style-image: url("javascript:alert(\'XSS\')");}</style><ul><li>blabla</li></ul>'
        );
        $styleTagCleanupStep = new StyleTagCleanupStep();
        $payload = $styleTagCleanupStep($payload);
        $this->assertStringNotContainsString('url("javascript:alert(\'XSS\')")', $payload->getContent());
    }
}
