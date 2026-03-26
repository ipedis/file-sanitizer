<?php

declare(strict_types=1);

namespace Ipedis\FileSanitizer\Pipeline\Steps;

use DOMDocument;
use DOMElement;
use Ipedis\FileSanitizer\Pipeline\Payload;

final class StyleTagCleanupStep extends CleanupStepAbstract
{
    protected function process(Payload $payload): Payload
    {
        $content = $payload->getContent();
        $domDocument = new DOMDocument('1.0', 'UTF-8');
        @$domDocument->loadHTML($content, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        $styles = $domDocument->getElementsByTagName('style');
        /** @var DOMElement $style */
        foreach ($styles as $style) {
            if (str_contains((string) $style->nodeValue, 'javascript') || str_contains((string) $style->nodeValue, 'script')) {
                $style->remove();
            }
        }

        return $payload->setContent((string) $domDocument->saveHTML());
    }
}
