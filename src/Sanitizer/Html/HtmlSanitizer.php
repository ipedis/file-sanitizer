<?php

declare(strict_types=1);

namespace Ipedis\FileSanitizer\Sanitizer\Html;

use Ipedis\FileSanitizer\Contract\SanitizerInterface;
use Ipedis\FileSanitizer\Pipeline\PipelineSanitizerAbstract;
use Ipedis\FileSanitizer\Pipeline\Steps\AttributeCleanupStep;
use Ipedis\FileSanitizer\Pipeline\Steps\DecodeTagCleanupStep;
use Ipedis\FileSanitizer\Pipeline\Steps\PhpTagCleanupStep;
use Ipedis\FileSanitizer\Pipeline\Steps\ScriptTagCleanupStep;
use Ipedis\FileSanitizer\Pipeline\Steps\StyleTagCleanupStep;

final class HtmlSanitizer extends PipelineSanitizerAbstract implements SanitizerInterface
{
    protected function getRegisteredCleanupStep(): array
    {
        return [
            DecodeTagCleanupStep::class,
            PhpTagCleanupStep::class,
            ScriptTagCleanupStep::class,
            AttributeCleanupStep::class,
            StyleTagCleanupStep::class,
        ];
    }
}
