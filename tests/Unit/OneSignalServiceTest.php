<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

class OneSignalServiceTest extends TestCase
{
    /**
     * Verify that the dispatch method sends "Authorization: Key ..." and not "Authorization: Basic ...".
     * Regression test for the HTTP 403 OneSignal error caused by using the wrong auth scheme.
     */
    public function test_authorization_header_uses_key_scheme(): void
    {
        $source = file_get_contents(__DIR__ . '/../../app/Services/OneSignalService.php');

        $this->assertStringContainsString(
            "'Authorization: Key '",
            $source,
            'OneSignalService must use "Authorization: Key" for the OneSignal REST API.'
        );

        $this->assertStringNotContainsString(
            "'Authorization: Basic '",
            $source,
            'OneSignalService must NOT use "Authorization: Basic" – OneSignal requires the Key scheme.'
        );
    }
}
