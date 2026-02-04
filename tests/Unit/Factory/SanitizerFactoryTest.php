<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Factory;

use Ipedis\FileSanitizer\Configuration\Configuration;
use Ipedis\FileSanitizer\Contract\SanitizerInterface;
use Ipedis\FileSanitizer\Exception\InvalidSanitizerTypeException;
use Ipedis\FileSanitizer\Factory\SanitizerFactory;
use PHPUnit\Framework\TestCase;

final class SanitizerFactoryTest extends TestCase
{

    public function testWithValidType(): void
    {
        $sanitizer = SanitizerFactory::build(type: 'html', configuration: new Configuration());
        $this->assertInstanceOf(SanitizerInterface::class, $sanitizer);
    }

    public function testWithInvalidType(): void
    {
        $this->expectException(InvalidSanitizerTypeException::class);
        SanitizerFactory::build(type: 'unknown', configuration: null);
    }

}
