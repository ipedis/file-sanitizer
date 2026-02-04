<?php

declare(strict_types=1);

namespace Ipedis\FileSanitizer\Exception;

use Exception;

final class InvalidCleanupStepException extends Exception
{
    public function __construct(string $step, int $code = 0)
    {
        $message = $step . ' is not a valid cleanup step';
        parent::__construct(message: $message, code: $code);
    }
}
