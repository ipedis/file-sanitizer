<?php

declare(strict_types=1);

namespace Ipedis\FileSanitizer\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;

final class StyleTagCleanupStep extends CleanupStepAbstract
{
    private const PATTERN = '#<style\b[^>]*>[\s\S]*?</style>#i';

    protected function process(Payload $payload): Payload
    {
        $content = $payload->getContent();

        return $payload->setContent(
            (string) preg_replace_callback(self::PATTERN, static function (array $match): string {
                $inner = strip_tags($match[0]);
                if (str_contains($inner, 'javascript') || str_contains($inner, 'script')) {
                    return '';
                }

                return $match[0];
            }, $content)
        );
    }
}
