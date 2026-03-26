<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Factory;

use Ipedis\FileSanitizer\Configuration\Configuration;
use Ipedis\FileSanitizer\Contract\SanitizerInterface;
use Ipedis\FileSanitizer\Exception\InvalidSanitizerTypeException;
use Ipedis\FileSanitizer\Factory\SanitizerFactory;
use Ipedis\FileSanitizer\Sanitizer\Html\HtmlSanitizer;
use Ipedis\FileSanitizer\Sanitizer\Xml\XmlSanitizer;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class SanitizerFactoryTest extends TestCase
{
    #[Test]
    public function it_builds_html_sanitizer(): void
    {
        $sanitizer = SanitizerFactory::build(type: 'html', configuration: new Configuration());
        $this->assertInstanceOf(SanitizerInterface::class, $sanitizer);
        $this->assertInstanceOf(HtmlSanitizer::class, $sanitizer);
    }

    #[Test]
    public function it_builds_xml_sanitizer(): void
    {
        $sanitizer = SanitizerFactory::build(type: 'xml', configuration: new Configuration());
        $this->assertInstanceOf(SanitizerInterface::class, $sanitizer);
        $this->assertInstanceOf(XmlSanitizer::class, $sanitizer);
    }

    #[Test]
    public function it_throws_for_invalid_type(): void
    {
        $this->expectException(InvalidSanitizerTypeException::class);
        SanitizerFactory::build(type: 'unknown', configuration: null);
    }

    #[Test]
    public function it_accepts_null_configuration(): void
    {
        $sanitizer = SanitizerFactory::build(type: 'html', configuration: null);
        $this->assertInstanceOf(SanitizerInterface::class, $sanitizer);
    }

    #[Test]
    public function it_throws_for_empty_type(): void
    {
        $this->expectException(InvalidSanitizerTypeException::class);
        SanitizerFactory::build(type: '', configuration: null);
    }
}
