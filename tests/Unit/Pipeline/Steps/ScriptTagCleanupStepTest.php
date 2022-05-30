<?php

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
        $cleanup = new ScriptTagCleanupStep();
        $payload = $cleanup($payload);
        $this->assertEmpty($payload->getContent());
    }
}
