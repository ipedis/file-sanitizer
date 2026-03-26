<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\DecodeTagCleanupStep;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class DecodeTagCleanupStepTest extends TestCase
{
    #[Test]
    public function it_decodes_encoded_html_tags(): void
    {
        $payload = Payload::build(
            content: '&lt;script&gt;alert("hacked");&lt;/script&gt;'
        );
        $decodeTagCleanupStep = new DecodeTagCleanupStep();
        $result = $decodeTagCleanupStep($payload);
        $this->assertSame('<script>alert("hacked");</script>', $result->getContent());
    }

    #[Test]
    public function it_decodes_ampersand_and_quotes(): void
    {
        $payload = Payload::build(
            content: '&amp; &quot;hello&quot;'
        );
        $decodeTagCleanupStep = new DecodeTagCleanupStep();
        $result = $decodeTagCleanupStep($payload);
        $this->assertSame('& "hello"', $result->getContent());
    }

    #[Test]
    public function it_preserves_content_without_entities(): void
    {
        $payload = Payload::build(
            content: '<p>plain text</p>'
        );
        $decodeTagCleanupStep = new DecodeTagCleanupStep();
        $result = $decodeTagCleanupStep($payload);
        $this->assertSame('<p>plain text</p>', $result->getContent());
    }

    #[Test]
    public function it_handles_empty_content(): void
    {
        $payload = Payload::build(content: '');
        $decodeTagCleanupStep = new DecodeTagCleanupStep();
        $result = $decodeTagCleanupStep($payload);
        $this->assertSame('', $result->getContent());
    }
}
