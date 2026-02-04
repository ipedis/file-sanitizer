<?php

declare(strict_types=1);

namespace Ipedis\Tests\Unit\Pipeline\Steps;

use Ipedis\FileSanitizer\Pipeline\Payload;
use Ipedis\FileSanitizer\Pipeline\Steps\CdataTagCleanupStep;
use PHPUnit\Framework\TestCase;

final class CdataTagCleanupStepTest extends TestCase
{
    public function testProcess(): void
    {
        $payload = Payload::build(
            content: '<![CDATA[ <s ]]>crip<![CDATA[ T> ]]>alert(document.cookie);<![CDATA[ </s ]]>CRIP<![CDATA[ T> ]]>'
        );
        $cdataTagCleanupStep = new CdataTagCleanupStep();
        $payload = $cdataTagCleanupStep($payload);
        $this->assertEmpty($payload->getContent());
    }
}
