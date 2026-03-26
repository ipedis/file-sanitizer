<?php

declare(strict_types=1);

namespace Ipedis\FileSanitizer\Sanitizer;

use Ipedis\FileSanitizer\Configuration\Configuration;
use Ipedis\FileSanitizer\Contract\SanitizerInterface;
use Ipedis\FileSanitizer\Exception\InvalidSanitizerTypeException;
use Ipedis\FileSanitizer\Factory\SanitizerFactory;
use Ipedis\FileSanitizer\Pipeline\Payload;

final readonly class Sanitize
{
    private SanitizerInterface $sanitizer;

    /**
     * @throws InvalidSanitizerTypeException
     */
    public function __construct(
        string $type,
        ?Configuration $configuration = null
    ) {
        $this->sanitizer = SanitizerFactory::build(type: $type, configuration: $configuration);
    }

    public function process(string $content): Payload
    {
        return $this->sanitizer->sanitize(content: $content);
    }
}
