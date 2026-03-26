<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\AttributeCleanupStep;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class AttributeCleanupStepTest extends TestCase
{
    #[Test]
    public function it_removes_encoded_javascript_from_attribute_value(): void
    {
        $payload = Payload::build(
            content: '<img src="jav&#x09;ascript:alert(\'hacked\');">'
        );
        $attributeCleanupStep = new AttributeCleanupStep();
        $result = $attributeCleanupStep($payload);
        $this->assertStringNotContainsString('javascript', $result->getContent());
        $this->assertStringNotContainsString('alert', $result->getContent());
        $this->assertStringContainsString('<img', $result->getContent());
    }

    #[Test]
    public function it_removes_on_event_attributes(): void
    {
        $payload = Payload::build(
            content: '<div onclick="alert(1)" onmouseover="alert(2)">text</div>'
        );
        $attributeCleanupStep = new AttributeCleanupStep();
        $result = $attributeCleanupStep($payload);
        $this->assertStringNotContainsString('onclick', $result->getContent());
        $this->assertStringNotContainsString('onmouseover', $result->getContent());
        $this->assertStringContainsString('text', $result->getContent());
    }

    #[Test]
    public function it_removes_onerror_attribute(): void
    {
        $payload = Payload::build(
            content: '<img src="valid.jpg" onerror="alert(\'xss\')">'
        );
        $attributeCleanupStep = new AttributeCleanupStep();
        $result = $attributeCleanupStep($payload);
        $this->assertStringNotContainsString('onerror', $result->getContent());
        $this->assertStringContainsString('valid.jpg', $result->getContent());
    }

    #[Test]
    public function it_clears_alert_in_attribute_value(): void
    {
        $payload = Payload::build(
            content: '<a href="javascript:alert(\'xss\')">link</a>'
        );
        $attributeCleanupStep = new AttributeCleanupStep();
        $result = $attributeCleanupStep($payload);
        $this->assertStringNotContainsString('alert', $result->getContent());
        $this->assertStringContainsString('link', $result->getContent());
    }

    #[Test]
    public function it_preserves_safe_attributes(): void
    {
        $payload = Payload::build(
            content: '<a href="https://example.com" class="link" id="main">safe</a>'
        );
        $attributeCleanupStep = new AttributeCleanupStep();
        $result = $attributeCleanupStep($payload);
        $this->assertStringContainsString('https://example.com', $result->getContent());
        $this->assertStringContainsString('class="link"', $result->getContent());
        $this->assertStringContainsString('id="main"', $result->getContent());
    }

    #[Test]
    public function it_handles_content_without_attributes(): void
    {
        $payload = Payload::build(
            content: '<p>simple text</p>'
        );
        $attributeCleanupStep = new AttributeCleanupStep();
        $result = $attributeCleanupStep($payload);
        $this->assertStringContainsString('simple text', $result->getContent());
    }

    #[Test]
    public function it_removes_multiple_event_handlers_on_same_element(): void
    {
        $payload = Payload::build(
            content: '<div onclick="a()" onload="b()" onmouseenter="c()">content</div>'
        );
        $attributeCleanupStep = new AttributeCleanupStep();
        $result = $attributeCleanupStep($payload);
        $this->assertStringNotContainsString('onclick', $result->getContent());
        $this->assertStringNotContainsString('onload', $result->getContent());
        $this->assertStringNotContainsString('onmouseenter', $result->getContent());
        $this->assertStringContainsString('content', $result->getContent());
    }
}
