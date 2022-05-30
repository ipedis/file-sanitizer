<?php

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
        $cleanup = new StyleTagCleanupStep();
        $payload = $cleanup($payload);
        $this->assertFalse(str_contains($payload->getContent(), 'url("javascript:alert(\'XSS\')")'));
    }
}
