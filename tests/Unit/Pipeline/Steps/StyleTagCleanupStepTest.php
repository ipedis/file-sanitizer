<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\StyleTagCleanupStep;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class StyleTagCleanupStepTest extends TestCase
{
    #[Test]
    public function it_removes_style_with_javascript_url(): void
    {
        $payload = Payload::build(
            content: '<style>li {list-style-image: url("javascript:alert(\'XSS\')");}</style><ul><li>blabla</li></ul>'
        );
        $styleTagCleanupStep = new StyleTagCleanupStep();
        $result = $styleTagCleanupStep($payload);
        $this->assertStringNotContainsString('javascript', $result->getContent());
        $this->assertStringContainsString('blabla', $result->getContent());
    }

    #[Test]
    public function it_removes_style_containing_script_keyword(): void
    {
        $payload = Payload::build(
            content: '<style>body { background: url("script:evil"); }</style><p>text</p>'
        );
        $styleTagCleanupStep = new StyleTagCleanupStep();
        $result = $styleTagCleanupStep($payload);
        $this->assertStringNotContainsString('<style>', $result->getContent());
        $this->assertStringContainsString('text', $result->getContent());
    }

    #[Test]
    public function it_preserves_safe_style_tags(): void
    {
        $payload = Payload::build(
            content: '<style>body { color: red; }</style><p>text</p>'
        );
        $styleTagCleanupStep = new StyleTagCleanupStep();
        $result = $styleTagCleanupStep($payload);
        $this->assertStringContainsString('color: red', $result->getContent());
        $this->assertStringContainsString('text', $result->getContent());
    }

    #[Test]
    public function it_handles_content_without_style(): void
    {
        $payload = Payload::build(
            content: '<p>no styles here</p>'
        );
        $styleTagCleanupStep = new StyleTagCleanupStep();
        $result = $styleTagCleanupStep($payload);
        $this->assertStringContainsString('no styles here', $result->getContent());
    }

    #[Test]
    public function it_removes_only_malicious_styles_among_multiple(): void
    {
        $payload = Payload::build(
            content: '<style>body { color: blue; }</style><style>div { background: url("javascript:alert(1)"); }</style><p>text</p>'
        );
        $styleTagCleanupStep = new StyleTagCleanupStep();
        $result = $styleTagCleanupStep($payload);
        $this->assertStringContainsString('color: blue', $result->getContent());
        $this->assertStringNotContainsString('javascript', $result->getContent());
    }
}
