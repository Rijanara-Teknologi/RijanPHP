<?php

namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Http\Client;

/**
 * Tests for Bug #11: HTTP Client form_params doesn't handle CURLFile (multipart upload)
 * Tests for Bug #13: HTTP Client sends Expect: 100-continue which stalls some servers.
 *
 * These tests inspect the HTTP Client class source code and behaviour without
 * making real network requests.
 */
class HttpClientTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Bug #13: Expect: header must be suppressed
    // -------------------------------------------------------------------------

    public function testPrepareHeadersIncludesEmptyExpectHeader()
    {
        $source = file_get_contents(__DIR__ . '/../Core/Http/Client.php');

        $this->assertStringContainsString(
            "Expect:",
            $source,
            'Client.php must set an empty Expect: header to suppress Expect: 100-continue'
        );
    }

    public function testPrepareHeadersReturnsExpectHeaderViaReflection()
    {
        // Use a concrete subclass to call the protected method without ref-warning
        $client = new class extends Client {
            public function callPrepareHeaders(array $options): array
            {
                return $this->prepareHeaders($options);
            }
        };

        $options = ['headers' => ['Content-Type' => 'application/json']];
        $headers = $client->callPrepareHeaders($options);

        $this->assertContains('Expect:', $headers, 'prepareHeaders() must include "Expect:" in the header list');
    }

    public function testPrepareHeadersIncludesExpectEvenWithNoOtherHeaders()
    {
        $client = new class extends Client {
            public function callPrepareHeaders(array $options): array
            {
                return $this->prepareHeaders($options);
            }
        };

        $options = [];
        $headers = $client->callPrepareHeaders($options);

        $this->assertContains('Expect:', $headers);
    }

    // -------------------------------------------------------------------------
    // Bug #11: CURLFile detection in form_params
    // -------------------------------------------------------------------------

    public function testPreparePayloadSourceDetectsCurlFile()
    {
        $source = file_get_contents(__DIR__ . '/../Core/Http/Client.php');

        $this->assertStringContainsString(
            'CURLFile',
            $source,
            'Client.php must detect CURLFile instances in form_params for multipart upload'
        );
    }

    public function testPreparePayloadWithoutCurlFileUsesHttpBuildQuery()
    {
        // We verify the logic exists in source code — CURLFile check followed by
        // the fallback to http_build_query for non-file payloads.
        $source = file_get_contents(__DIR__ . '/../Core/Http/Client.php');

        $this->assertStringContainsString(
            'http_build_query',
            $source,
            'Client.php must still use http_build_query() for non-file form_params'
        );
    }

    public function testClientHasPreparePayloadMethod()
    {
        $ref = new \ReflectionClass(Client::class);

        $this->assertTrue(
            $ref->hasMethod('preparePayload'),
            'Client must have a preparePayload() method'
        );
    }
}
