<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Pipeline;

use Ipedis\FileSanitizer\Pipeline\Payload;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class PayloadTest extends TestCase
{
    #[Test]
    public function it_builds_with_content(): void
    {
        $payload = Payload::build('test content');
        $this->assertSame('test content', $payload->getContent());
    }

    #[Test]
    public function it_preserves_original_content(): void
    {
        $payload = Payload::build('original');
        $this->assertSame('original', $payload->originalContent);
    }

    #[Test]
    public function it_updates_content_via_set_content(): void
    {
        $payload = Payload::build('initial');
        $result = $payload->setContent('modified');
        $this->assertSame('modified', $result->getContent());
    }

    #[Test]
    public function set_content_preserves_original_content(): void
    {
        $payload = Payload::build('original');
        $payload->setContent('changed');
        $this->assertSame('original', $payload->originalContent);
    }

    #[Test]
    public function set_content_returns_same_instance(): void
    {
        $payload = Payload::build('initial');
        $result = $payload->setContent('modified');
        $this->assertSame($payload, $result);
    }

    #[Test]
    public function it_handles_empty_content(): void
    {
        $payload = Payload::build('');
        $this->assertSame('', $payload->getContent());
        $this->assertSame('', $payload->originalContent);
    }

    #[Test]
    public function it_handles_html_content(): void
    {
        $html = '<div class="test"><p>Hello & world</p></div>';
        $payload = Payload::build($html);
        $this->assertSame($html, $payload->getContent());
    }

    #[Test]
    public function original_content_remains_after_multiple_updates(): void
    {
        $payload = Payload::build('first');
        $payload->setContent('second');
        $payload->setContent('third');
        $this->assertSame('first', $payload->originalContent);
        $this->assertSame('third', $payload->getContent());
    }
}
