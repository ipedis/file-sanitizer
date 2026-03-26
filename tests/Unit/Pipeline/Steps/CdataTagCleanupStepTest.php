<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\CdataTagCleanupStep;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class CdataTagCleanupStepTest extends TestCase
{
    #[Test]
    public function it_removes_split_cdata_sections(): void
    {
        $payload = Payload::build(
            content: '<![CDATA[ <s ]]>crip<![CDATA[ T> ]]>alert(document.cookie);<![CDATA[ </s ]]>CRIP<![CDATA[ T> ]]>'
        );
        $cdataTagCleanupStep = new CdataTagCleanupStep();
        $result = $cdataTagCleanupStep($payload);
        $this->assertEmpty($result->getContent());
    }

    #[Test]
    public function it_preserves_content_without_cdata(): void
    {
        $payload = Payload::build(
            content: '<root><item>safe content</item></root>'
        );
        $cdataTagCleanupStep = new CdataTagCleanupStep();
        $result = $cdataTagCleanupStep($payload);
        $this->assertSame('<root><item>safe content</item></root>', $result->getContent());
    }

    #[Test]
    public function it_handles_empty_content(): void
    {
        $payload = Payload::build(content: '');
        $cdataTagCleanupStep = new CdataTagCleanupStep();
        $result = $cdataTagCleanupStep($payload);
        $this->assertSame('', $result->getContent());
    }

    #[Test]
    public function it_removes_cdata_with_content_between_pairs(): void
    {
        $payload = Payload::build(
            content: "<![CDATA[ <s ]]>crip<![CDATA[ T> ]]>\nmalicious content\n<![CDATA[ </s ]]>CRIP<![CDATA[ T> ]]>"
        );
        $cdataTagCleanupStep = new CdataTagCleanupStep();
        $result = $cdataTagCleanupStep($payload);
        $this->assertStringNotContainsString('CDATA', $result->getContent());
        $this->assertStringNotContainsString('malicious', $result->getContent());
    }
}
