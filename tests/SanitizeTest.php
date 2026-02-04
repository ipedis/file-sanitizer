<?php

declare(strict_types=1);

namespace Ipedis\Tests;

use DOMDocument;
use DOMNode;
use Ipedis\FileSanitizer\Configuration\Configuration;
use Ipedis\FileSanitizer\Pipeline\Steps\PhpTagCleanupStep;
use Ipedis\FileSanitizer\Sanitizer\Sanitize;
use Ipedis\Tests\Data\CustomCleanupStepTest;
use PHPUnit\Framework\TestCase;

final class SanitizeTest extends TestCase
{
    /**
     * @dataProvider provideFileData
     */
    public function testSanitize(array $input, array $output, string $type): void
    {
        $sanitize = new Sanitize(type: $type);
        $inputFileContent = file_get_contents($input['path']);
        $sanitized = $sanitize->process($inputFileContent);
        $outputFileContent = file_get_contents($output['path']);
        $this->assertTrue($this->hasSameContent($sanitized->getContent(), $outputFileContent, $type));
    }

    public function testWithIgnoredConfiguration(): void
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

    public function testWithCustomStep(): void
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

    public function provideFileData(): \Iterator
    {
        yield 0 => [
            'malicious_file' => [
                'name' => 'malicious_html.html',
                'path' => __DIR__ . '/Data/Input/OnBody/malicious_html.html'
            ],
            'sanitized_file' => [
                'name' => 'sanitized_html.html',
                'path' => __DIR__ . '/Data/Output/OnBody/sanitized_html.html'

            ],
            'type' => 'html'
        ];
        yield 1 => [
            'malicious_file' => [
                'name' => 'malicious_xml.xml',
                'path' => __DIR__ . '/Data/Input/OnBody/malicious_xml.xml'
            ],
            'sanitized_file' => [
                'name' => 'sanitized_xml.xml',
                'path' => __DIR__ . '/Data/Output/OnBody/sanitized_xml.xml'
            ],
            'type' => 'xml'
        ];
        yield 2 => [
            'malicious_file' => [
                'name' => 'malicious_html.html',
                'path' => __DIR__ . '/Data/Input/OnAttr/malicious_html.html'
            ],
            'sanitized_file' => [
                'name' => 'sanitized_html.html',
                'path' => __DIR__ . '/Data/Output/OnAttr/sanitized_html.html'
            ],
            'type' => 'html'
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
            if (!$this->isSameNode($inputNodes[$i], $outputNodes[$i])) {
                return false;
            }
        }

        return true;
    }

    private function isSameNode(DOMNode $nodeToCompare, DOMNode $nodeReferred): bool
    {
        if ($nodeToCompare->tagName !== $nodeReferred->tagName) {
            return false;
        }

        $comparedValue = str_replace(' ', '', str_replace("\n", '', $nodeToCompare->nodeValue));
        $referredValue = str_replace(' ', '', str_replace("\n", '', $nodeReferred->nodeValue));
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
