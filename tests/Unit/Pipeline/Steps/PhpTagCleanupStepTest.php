<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\PhpTagCleanupStep;
use PHPUnit\Framework\TestCase;

final class PhpTagCleanupStepTest extends TestCase
{
    public function testProcess(): void
    {
        $payload = Payload::build(
            content: "<?php echo 'hacked' ?>"
        );
        $phpTagCleanupStep = new PhpTagCleanupStep();
        $payload = $phpTagCleanupStep($payload);
        $this->assertStringNotContainsString('<?php', $payload->getContent());
        $this->assertStringNotContainsString('?>', $payload->getContent());
    }
}
