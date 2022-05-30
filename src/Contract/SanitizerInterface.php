<?php

namespace Ipedis\FileSanitizer\Contract;

use Ipedis\FileSanitizer\Pipeline\Payload;

interface SanitizerInterface
{
    public function sanitize(string $content): Payload;
}
