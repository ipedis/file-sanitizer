<?php

namespace Ipedis\FileSanitizer\Pipeline\Steps;

use DOMDocument;
use DOMElement;
use Ipedis\FileSanitizer\Pipeline\Payload;

final class AttributeCleanupStep extends CleanupStepAbstract
{
    const ALERT_PATTERN = '#alert\((.*?)\)#';

    protected function process(Payload $payload): Payload
    {
        $content = $payload->getContent();
        $dom = new DOMDocument('1.0', 'UTF-8');
        @$dom->loadHTML($content, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        $elements = $dom->getElementsByTagName('*');
        /** @var DOMElement $element */
        foreach ($elements as $element) {
            if ($element->hasAttributes()) {
                foreach ($element->attributes as $attribute) {
                    //remove all js event on attribute
                    if (str_starts_with($attribute->name, 'on')) {
                        $element->removeAttribute($attribute->name);
                    }
                    //remove javascript code on attribute value
                    $attrValue = preg_replace('/\s+/', '', html_entity_decode($attribute->value));
                    if (preg_match(self::ALERT_PATTERN, $attrValue) || str_contains($attrValue, 'javascript')) {
                        $attribute->value = '';
                    }
                }
            }
        }

        return $payload->setContent($dom->saveHTML());
    }
}
