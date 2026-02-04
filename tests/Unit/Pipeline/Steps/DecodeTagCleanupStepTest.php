<?php

declare(strict_types=1);

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
        $decodeTagCleanupStep = new DecodeTagCleanupStep();
        $payload = $decodeTagCleanupStep($payload);
        $this->assertSame('<script>alert("hacked");</script>', $payload->getContent());
    }

}
