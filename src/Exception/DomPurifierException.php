<?php

namespace Ipedis\FileSanitizer\Exception;

use \Exception;

final class DomPurifierException extends Exception
{
    public function __construct(string $message, int $code = 0)
    {
        parent::__construct(message: $message, code: $code);
    }
}
