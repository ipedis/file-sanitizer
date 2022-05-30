<?php

namespace Ipedis\FileSanitizer\Exception;

use \Exception;

final class InvalidSanitizerTypeException extends Exception
{
    public function __construct(string $type, int $code = 0)
    {
        $message = "$type is not a valid sanitizer type";
        parent::__construct(message: $message, code: $code);
    }
}
