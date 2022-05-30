<?php

namespace Ipedis\Tests\Unit\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\PhpTagCleanupStep;
use PHPUnit\Framework\TestCase;

final class PhpTagCleanupStepTest extends TestCase
{
    public function testProcess(): void
    {
        $payload = Payload::build(
            content: '<?php echo \'hacked\' ?>'
        );
        $cleanup = new PhpTagCleanupStep();
        $payload = $cleanup($payload);
        $this->assertFalse(str_contains($payload->getContent(), '<?php'));
        $this->assertFalse(str_contains($payload->getContent(), '?>'));
    }
}
