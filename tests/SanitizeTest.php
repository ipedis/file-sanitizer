<?php

declare(strict_types=1);

namespace Ipedis\Tests;

use DOMDocument;
use DOMElement;
use DOMNode;
use Ipedis\FileSanitizer\Configuration\Configuration;
use Ipedis\FileSanitizer\Pipeline\Steps\PhpTagCleanupStep;
use Ipedis\FileSanitizer\Sanitizer\Sanitize;
use Ipedis\Tests\Data\CustomCleanupStepTest;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class SanitizeTest extends TestCase
{
    /**
     * @param array{name: string, path: string} $input
     * @param array{name: string, path: string} $output
     */
    #[DataProvider('provideFileData')]
    #[Test]
    public function sanitize(array $input, array $output, string $type): void
    {
        $sanitize = new Sanitize(type: $type);
        $inputFileContent = (string) file_get_contents($input['path']);
        $sanitized = $sanitize->process($inputFileContent);
        $outputFileContent = (string) file_get_contents($output['path']);
        $this->assertTrue($this->hasSameContent($sanitized->getContent(), $outputFileContent, $type));
    }

    /**
     */
    #[Test]
    public function with_ignored_configuration(): void
    {
        $sanitize = new Sanitize(
            type: 'html',
            configuration: new Configuration(
                ignoredSteps: [PhpTagCleanupStep::class]
            )
        );
        $dirty = <<<CONTENT
<!DOCTYPE html>
<html lang="en">
<body>
<?php echo 'test' ?>
</body>
<script type="text/javascript">
    alert("hacked");
</script>
</html>
CONTENT;
        $cleaned = <<<CONTENT
<!DOCTYPE html>
<html lang="en">
<body>
<?php echo 'test' ?>
</body>
</html>
CONTENT;
        $sanitized = $sanitize->process($dirty);
        $this->assertTrue($this->hasSameContent($sanitized->getContent(), $cleaned, 'html'));
    }

    /**
     */
    #[Test]
    public function with_custom_step(): void
    {
        $sanitize = new Sanitize(
            type: 'html',
            configuration: new Configuration(
                customSteps: [CustomCleanupStepTest::class]
            )
        );
        $dirty = 'content';
        $sanitized = $sanitize->process($dirty);
        $this->assertSame('new custom step', $sanitized->getContent());
    }

    public static function provideFileData(): \Iterator
    {
        yield 'html body sanitization' => [
            'input' => [
                'name' => 'malicious_html.html',
                'path' => __DIR__ . '/Data/Input/OnBody/malicious_html.html',
            ],
            'output' => [
                'name' => 'sanitized_html.html',
                'path' => __DIR__ . '/Data/Output/OnBody/sanitized_html.html',
            ],
            'type' => 'html',
        ];
        yield 'xml body sanitization' => [
            'input' => [
                'name' => 'malicious_xml.xml',
                'path' => __DIR__ . '/Data/Input/OnBody/malicious_xml.xml',
            ],
            'output' => [
                'name' => 'sanitized_xml.xml',
                'path' => __DIR__ . '/Data/Output/OnBody/sanitized_xml.xml',
            ],
            'type' => 'xml',
        ];
        yield 'html attribute sanitization' => [
            'input' => [
                'name' => 'malicious_html.html',
                'path' => __DIR__ . '/Data/Input/OnAttr/malicious_html.html',
            ],
            'output' => [
                'name' => 'sanitized_html.html',
                'path' => __DIR__ . '/Data/Output/OnAttr/sanitized_html.html',
            ],
            'type' => 'html',
        ];
    }

    private function hasSameContent(string $input, string $output, string $type): bool
    {
        $domInput = new DOMDocument('1.0', 'UTF-8');
        $domOutput = new DOMDocument('1.0', 'UTF-8');

        if ('html' === $type) {
            @$domInput->loadHTML('<?xml encoding="utf-8" ?>' . $input);
            @$domOutput->loadHTML('<?xml encoding="utf-8" ?>' . $output);
        } else {
            @$domInput->loadXML($input);
            @$domOutput->loadXML($output);
        }

        $inputNodes = $domInput->getElementsByTagName('*');
        $outputNodes = $domOutput->getElementsByTagName('*');

        if (count($inputNodes) !== count($outputNodes)) {
            return false;
        }

        $counter = count($inputNodes);

        //check one by one all element
        for ($i = 0; $i < $counter; ++$i) {
            $inputNode = $inputNodes[$i];
            $outputNode = $outputNodes[$i];
            if (!$inputNode instanceof DOMElement || !$outputNode instanceof DOMElement) {
                continue;
            }
            if (!$this->isSameNode($inputNode, $outputNode)) {
                return false;
            }
        }

        return true;
    }

    private function isSameNode(DOMElement $nodeToCompare, DOMElement $nodeReferred): bool
    {
        if ($nodeToCompare->tagName !== $nodeReferred->tagName) {
            return false;
        }

        $comparedValue = str_replace(' ', '', str_replace("\n", '', (string) $nodeToCompare->nodeValue));
        $referredValue = str_replace(' ', '', str_replace("\n", '', (string) $nodeReferred->nodeValue));
        if (trim($comparedValue) !== trim($referredValue)) {
            return false;
        }

        if ($nodeToCompare->prefix !== $nodeReferred->prefix) {
            return false;
        }

        if (!$nodeToCompare->hasAttributes() && $nodeReferred->hasAttributes()) {
            return false;
        }

        if (!$nodeReferred->hasAttributes() && $nodeToCompare->hasAttributes()) {
            return false;
        }

        foreach ($nodeToCompare->attributes as $attribute) {
            $findAttributeWithSameValue = false;
            foreach ($nodeReferred->attributes as $attributeReferred) {
                if (
                    $attributeReferred->name === $attribute->name
                    && ($attributeReferred->value === $attribute->value)
                ) {
                    $findAttributeWithSameValue = true;
                }
            }

            if (!$findAttributeWithSameValue) {
                return false;
            }
        }

        return true;
    }

}
