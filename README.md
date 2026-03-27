# File Sanitizer

[![CI](https://github.com/ipedis/file-sanitizer/actions/workflows/ci.yml/badge.svg)](https://github.com/ipedis/file-sanitizer/actions/workflows/ci.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/ipedis/file-sanitizer.svg)](https://packagist.org/packages/ipedis/file-sanitizer)
[![PHP Version](https://img.shields.io/packagist/php-v/ipedis/file-sanitizer.svg)](https://packagist.org/packages/ipedis/file-sanitizer)
[![License](https://img.shields.io/packagist/l/ipedis/file-sanitizer.svg)](https://packagist.org/packages/ipedis/file-sanitizer)

Pipeline-based HTML and XML sanitizer for PHP. Removes script tags, event handlers, PHP tags, CDATA injections, and other XSS vectors through a configurable chain of cleanup steps.

## Installation

```bash
composer require ipedis/file-sanitizer
```

## Quick Start

```php
use Ipedis\FileSanitizer\Sanitizer\Sanitize;

$sanitizer = new Sanitize(type: 'html');
$result = $sanitizer->process('<div onclick="alert(1)"><script>evil()</script>Hello</div>');

echo $result->getContent(); // <div>Hello</div>
```

### XML sanitization

```php
$sanitizer = new Sanitize(type: 'xml');
$result = $sanitizer->process($xmlContent);
```

### Custom configuration

```php
use Ipedis\FileSanitizer\Configuration\Configuration;
use Ipedis\FileSanitizer\Pipeline\Steps\PhpTagCleanupStep;

// Skip specific steps
$config = new Configuration(
    ignoredSteps: [PhpTagCleanupStep::class],
);

$sanitizer = new Sanitize(type: 'html', configuration: $config);
```

### Custom cleanup steps

```php
use Ipedis\FileSanitizer\Pipeline\Steps\CleanupStepAbstract;
use Ipedis\FileSanitizer\Pipeline\Payload;

class MyCustomStep extends CleanupStepAbstract
{
    protected function process(Payload $payload): Payload
    {
        $content = preg_replace('/pattern/', '', $payload->getContent());
        return $payload->setContent($content);
    }
}

$config = new Configuration(customSteps: [MyCustomStep::class]);
$sanitizer = new Sanitize(type: 'html', configuration: $config);
```

## Cleanup Steps

### HTML pipeline

| Step | What it removes |
|------|----------------|
| `DecodeTagCleanupStep` | Decodes HTML entities (`&lt;script&gt;` → `<script>`) |
| `PhpTagCleanupStep` | PHP tags (`<?php`, `<?`, `?>`) |
| `ScriptTagCleanupStep` | `<script>` blocks |
| `AttributeCleanupStep` | Event handlers (`onclick`, `onerror`...) and `javascript:` URLs |
| `StyleTagCleanupStep` | `<style>` blocks containing JavaScript |

### XML pipeline

| Step | What it removes |
|------|----------------|
| `DecodeTagCleanupStep` | Decodes HTML entities |
| `CdataTagCleanupStep` | CDATA injection patterns |
| `ScriptTagCleanupStep` | `<script>` blocks |

## Compatibility

| PHP | Status |
|-----|--------|
| 8.2 | ✅ |
| 8.3 | ✅ |
| 8.4 | ✅ |
| 8.5 | ✅ |

## Local Development

Requires [Docker](https://www.docker.com/).

```bash
make up        # Start container
make install   # Install dependencies
make qa        # Run full QA suite (rector + pint + phpstan + tests)
```

Available targets:

| Command | Description |
|---------|-------------|
| `make up` | Start container |
| `make down` | Stop container |
| `make install` | Install Composer dependencies |
| `make update` | Update Composer dependencies |
| `make test` | Run PHPUnit tests |
| `make phpstan` | Run static analysis (level max) |
| `make pint` | Fix code style (PSR-12) |
| `make rector` | Run automated refactoring |
| `make qa` | Run all checks |
| `make shell` | Open container shell |

## Disclaimer

This package is maintained by [Ipedis](https://www.ipedis.com). It is provided as-is under the terms of its license.
