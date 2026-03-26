<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\ScriptTagCleanupStep;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ScriptTagCleanupStepTest extends TestCase
{
    #[Test]
    public function it_removes_script_tag_with_type_attribute(): void
    {
        $payload = Payload::build(
            content: '<script type="text/javascript">alert("hacked");</script>'
        );
        $step = new ScriptTagCleanupStep();
        $result = $step($payload);
        $this->assertEmpty($result->getContent());
    }

    #[Test]
    public function it_removes_script_tag_without_attributes(): void
    {
        $payload = Payload::build(
            content: '<script>document.cookie;</script>'
        );
        $step = new ScriptTagCleanupStep();
        $result = $step($payload);
        $this->assertEmpty($result->getContent());
    }

    #[Test]
    public function it_removes_multiple_script_tags(): void
    {
        $payload = Payload::build(
            content: '<p>text</p><script>a();</script><p>more</p><script>b();</script>'
        );
        $step = new ScriptTagCleanupStep();
        $result = $step($payload);
        $this->assertStringNotContainsString('<script', $result->getContent());
        $this->assertStringContainsString('text', $result->getContent());
        $this->assertStringContainsString('more', $result->getContent());
    }

    #[Test]
    public function it_removes_script_tag_case_insensitive(): void
    {
        $payload = Payload::build(
            content: '<SCRIPT>alert("xss");</SCRIPT>'
        );
        $step = new ScriptTagCleanupStep();
        $result = $step($payload);
        $this->assertStringNotContainsString('alert', $result->getContent());
    }

    #[Test]
    public function it_preserves_content_without_script(): void
    {
        $payload = Payload::build(
            content: '<p>safe content</p>'
        );
        $step = new ScriptTagCleanupStep();
        $result = $step($payload);
        $this->assertSame('<p>safe content</p>', $result->getContent());
    }

    #[Test]
    public function it_removes_script_with_src_attribute(): void
    {
        $payload = Payload::build(
            content: '<script src="https://evil.com/xss.js"></script>'
        );
        $step = new ScriptTagCleanupStep();
        $result = $step($payload);
        $this->assertEmpty($result->getContent());
    }

    #[Test]
    public function it_removes_multiline_script(): void
    {
        $payload = Payload::build(
            content: "<script>\nvar x = 1;\nalert(x);\n</script>"
        );
        $step = new ScriptTagCleanupStep();
        $result = $step($payload);
        $this->assertEmpty($result->getContent());
    }
}
