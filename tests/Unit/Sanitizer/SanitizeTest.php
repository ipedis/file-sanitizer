<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Sanitizer;

use Ipedis\FileSanitizer\Configuration\Configuration;
use Ipedis\FileSanitizer\Exception\InvalidSanitizerTypeException;
use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\ScriptTagCleanupStep;
use Ipedis\FileSanitizer\Sanitizer\Sanitize;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class SanitizeTest extends TestCase
{
    #[Test]
    public function it_processes_html_content(): void
    {
        $sanitize = new Sanitize(type: 'html');
        $result = $sanitize->process('<p>hello</p>');
        $this->assertInstanceOf(Payload::class, $result);
        $this->assertStringContainsString('hello', $result->getContent());
    }

    #[Test]
    public function it_processes_xml_content(): void
    {
        $sanitize = new Sanitize(type: 'xml');
        $result = $sanitize->process('<root><item>data</item></root>');
        $this->assertInstanceOf(Payload::class, $result);
        $this->assertStringContainsString('data', $result->getContent());
    }

    #[Test]
    public function it_throws_for_invalid_type(): void
    {
        $this->expectException(InvalidSanitizerTypeException::class);
        new Sanitize(type: 'pdf');
    }

    #[Test]
    public function it_sanitizes_html_with_script(): void
    {
        $sanitize = new Sanitize(type: 'html');
        $result = $sanitize->process('<p>text</p><script>alert(1)</script>');
        $this->assertStringNotContainsString('<script', $result->getContent());
        $this->assertStringContainsString('text', $result->getContent());
    }

    #[Test]
    public function it_sanitizes_xml_with_script(): void
    {
        $sanitize = new Sanitize(type: 'xml');
        $result = $sanitize->process('<root><script>alert(1)</script><item>data</item></root>');
        $this->assertStringNotContainsString('<script', $result->getContent());
    }

    #[Test]
    public function it_accepts_configuration(): void
    {
        $configuration = new Configuration(ignoredSteps: [ScriptTagCleanupStep::class]);
        $sanitize = new Sanitize(type: 'html', configuration: $configuration);
        $result = $sanitize->process('<script>alert(1)</script>');
        // Script step is ignored, so decoded script tag remains
        $this->assertInstanceOf(Payload::class, $result);
    }

    #[Test]
    public function it_accepts_null_configuration(): void
    {
        $sanitize = new Sanitize(type: 'html');
        $result = $sanitize->process('<p>text</p>');
        $this->assertStringContainsString('text', $result->getContent());
    }
}
