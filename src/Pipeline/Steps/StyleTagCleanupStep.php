<?php

namespace Ipedis\FileSanitizer\Pipeline\Steps;

use DOMDocument;
use DOMElement;
use Ipedis\FileSanitizer\Pipeline\Payload;

final class StyleTagCleanupStep extends CleanupStepAbstract
{
    protected function process(Payload $payload): Payload
    {
        $content = $payload->getContent();
        $dom = new DOMDocument('1.0', 'UTF-8');
        @$dom->loadHTML($content, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        $styles = $dom->getElementsByTagName('style');
        /** @var DOMElement $style */
        foreach ($styles as $style) {
            if (str_contains($style->nodeValue, 'javascript') || str_contains($style->nodeValue, 'script')) {
                $style->remove();
            }
        }

        return $payload->setContent($dom->saveHTML());
    }
}
