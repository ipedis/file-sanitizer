<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\PhpTagCleanupStep;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class PhpTagCleanupStepTest extends TestCase
{
    #[Test]
    public function it_removes_php_tags(): void
    {
        $payload = Payload::build(
            content: "<?php echo 'hacked' ?>"
        );
        $phpTagCleanupStep = new PhpTagCleanupStep();
        $result = $phpTagCleanupStep($payload);
        $this->assertStringNotContainsString('<?php', $result->getContent());
        $this->assertStringNotContainsString('?>', $result->getContent());
    }

    #[Test]
    public function it_removes_short_open_tags(): void
    {
        $payload = Payload::build(
            content: "<? echo 'hacked' ?>"
        );
        $phpTagCleanupStep = new PhpTagCleanupStep();
        $result = $phpTagCleanupStep($payload);
        $this->assertStringNotContainsString('<?', $result->getContent());
        $this->assertStringNotContainsString('?>', $result->getContent());
    }

    #[Test]
    public function it_removes_multiple_php_blocks(): void
    {
        $payload = Payload::build(
            content: "<?php echo 'a' ?> text <?php echo 'b' ?>"
        );
        $phpTagCleanupStep = new PhpTagCleanupStep();
        $result = $phpTagCleanupStep($payload);
        $this->assertStringNotContainsString('<?php', $result->getContent());
        $this->assertStringContainsString('text', $result->getContent());
    }

    #[Test]
    public function it_preserves_content_without_php(): void
    {
        $payload = Payload::build(
            content: '<p>no php here</p>'
        );
        $phpTagCleanupStep = new PhpTagCleanupStep();
        $result = $phpTagCleanupStep($payload);
        $this->assertSame('<p>no php here</p>', $result->getContent());
    }

    #[Test]
    public function it_preserves_inner_content_of_php_block(): void
    {
        $payload = Payload::build(
            content: "<?php echo 'hacked' ?>"
        );
        $phpTagCleanupStep = new PhpTagCleanupStep();
        $result = $phpTagCleanupStep($payload);
        $this->assertStringContainsString("echo 'hacked'", trim($result->getContent()));
    }
}
