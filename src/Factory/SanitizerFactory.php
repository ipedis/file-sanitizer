<?php

declare(strict_types=1);

namespace Ipedis\FileSanitizer\Factory;

use Ipedis\FileSanitizer\Configuration\Configuration;
use Ipedis\FileSanitizer\Contract\SanitizerInterface;
use Ipedis\FileSanitizer\Exception\InvalidSanitizerTypeException;
use Ipedis\FileSanitizer\Sanitizer\Html\HtmlSanitizer;
use Ipedis\FileSanitizer\Sanitizer\Xml\XmlSanitizer;

final class SanitizerFactory
{
    private const HTML = 'html';

    private const XML = 'xml';

    /**
     * @throws InvalidSanitizerTypeException
     */
    public static function build(string $type, ?Configuration $configuration): SanitizerInterface
    {
        return match ($type) {
            self::HTML => new HtmlSanitizer($configuration),
            self::XML => new XmlSanitizer($configuration),
            default => throw new InvalidSanitizerTypeException(type: $type)
        };
    }
}
