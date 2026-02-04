<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\ScriptTagCleanupStep;
use PHPUnit\Framework\TestCase;

final class ScriptTagCleanupStepTest extends TestCase
{
    public function testProcess(): void
    {
        $payload = Payload::build(
            content: '<script type="text/javascript">alert("hacked");</script>'
        );
        $scriptTagCleanupStep = new ScriptTagCleanupStep();
        $payload = $scriptTagCleanupStep($payload);
        $this->assertEmpty($payload->getContent());
    }
}
