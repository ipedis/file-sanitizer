<?php

declare(strict_types=1);

namespace Ipedis\FileSanitizer\Contract;

use Ipedis\FileSanitizer\Pipeline\Payload;

interface SanitizerInterface
{
    public function sanitize(string $content): Payload;
}
