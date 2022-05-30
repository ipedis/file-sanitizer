File-sanitizer
--

this library is useful to clean the contents of a file, against xss attacks and malicious codes.

File supported currently : html and xml.

Usage
==

use the service `Sanitize`.

```phpt
$sanitize = new Sanitize(type: 'html' );
$dirty = 'dircty content';
$sanitized = $sanitize->process($dirty);
```

Advanced:

we can configure the sanitizer using the class <a href="src/Configuration/Configuration.php">configuration</a>
to ignore some cleanupSteps or add customSteps .

```phpt
$configuration = new Configuration(
            ignoredSteps: [CdataTagCleanupStep::class, DomPurifierCleanupStep::class],
        );
$sanitize = new Sanitize(type: 'html', configuration: $configuration);
```

Test
==

`./vendor/bin/phpunit tests/SanitizeTest.php`

We can play around data, input should be same of output after clean up.

**NB:** need to update data provider on `tests/SanitizeTest.php` 
if you want to add some test file 


