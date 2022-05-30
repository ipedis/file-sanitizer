<?php

namespace Ipedis\Tests\Unit\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\DecodeTagCleanupStep;
use PHPUnit\Framework\TestCase;

final class DecodeTagCleanupStepTest extends TestCase
{
    public function testProcess(): void
    {
        $payload = Payload::build(
            content: '&lt;script&gt;alert("hacked");&lt;/script&gt;'
        );
        $cleanup = new DecodeTagCleanupStep();
        $payload = $cleanup($payload);
        $this->assertEquals('<script>alert("hacked");</script>', $payload->getContent());
    }

}
