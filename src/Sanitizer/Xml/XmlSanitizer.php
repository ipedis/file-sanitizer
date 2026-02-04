<?php

declare(strict_types=1);

namespace Ipedis\FileSanitizer\Sanitizer\Xml;


use Ipedis\FileSanitizer\Contract\SanitizerInterface;
use Ipedis\FileSanitizer\Pipeline\PipelineSanitizerAbstract;
use Ipedis\FileSanitizer\Pipeline\Steps\CdataTagCleanupStep;
use Ipedis\FileSanitizer\Pipeline\Steps\DecodeTagCleanupStep;
use Ipedis\FileSanitizer\Pipeline\Steps\ScriptTagCleanupStep;

final class XmlSanitizer extends PipelineSanitizerAbstract implements SanitizerInterface
{
    protected function getRegisteredCleanupStep(): array
    {
        return [
            DecodeTagCleanupStep::class,
            CdataTagCleanupStep::class,
            ScriptTagCleanupStep::class
        ];
    }
}
