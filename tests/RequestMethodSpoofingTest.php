<?php

namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Http\Request;

/**
 * Tests for Bug #8: HTTP method spoofing via hidden _method input.
 *
 * HTML forms can only send GET and POST. Routes registered with Router::put(),
 * Router::patch(), or Router::delete() need a _method hidden field so the
 * framework can detect the intended HTTP verb.
 *
 * Request::method() must:
 *  - Return the spoofed method (PUT/PATCH/DELETE) when _method is present in POST
 *  - Reject invalid or non-spoofable values
 *  - Leave GET requests untouched
 */
class RequestMethodSpoofingTest extends TestCase
{
    private function makeRequest(array $post = [], array $server = []): Request
    {
        $server = array_merge(['REQUEST_METHOD' => 'POST'], $server);

        // Use reflection to inject values without going through $_POST / $_SERVER superglobals
        $req = new Request();
        $ref = new \ReflectionClass($req);

        $postProp = $ref->getProperty('post');
        $postProp->setAccessible(true);
        $postProp->setValue($req, $post);

        $serverProp = $ref->getProperty('server');
        $serverProp->setAccessible(true);
        $serverProp->setValue($req, $server);

        $jsonProp = $ref->getProperty('json');
        $jsonProp->setAccessible(true);
        $jsonProp->setValue($req, []);

        return $req;
    }

    // -------------------------------------------------------------------------
    // Spoofing via POST _method field
    // -------------------------------------------------------------------------

    public function testPostWithMethodPutReturnsPut()
    {
        $req = $this->makeRequest(['_method' => 'PUT']);
        $this->assertSame('PUT', $req->method());
    }

    public function testPostWithMethodPatchReturnsPatch()
    {
        $req = $this->makeRequest(['_method' => 'PATCH']);
        $this->assertSame('PATCH', $req->method());
    }

    public function testPostWithMethodDeleteReturnsDelete()
    {
        $req = $this->makeRequest(['_method' => 'DELETE']);
        $this->assertSame('DELETE', $req->method());
    }

    public function testSpoofedMethodIsCaseInsensitive()
    {
        $req = $this->makeRequest(['_method' => 'put']);
        $this->assertSame('PUT', $req->method());

        $req2 = $this->makeRequest(['_method' => 'Patch']);
        $this->assertSame('PATCH', $req2->method());
    }

    // -------------------------------------------------------------------------
    // Non-spoofable / invalid values must NOT override the method
    // -------------------------------------------------------------------------

    public function testPostWithInvalidMethodValueReturnsPost()
    {
        $req = $this->makeRequest(['_method' => 'GET']);
        $this->assertSame('POST', $req->method());
    }

    public function testPostWithUnknownMethodValueReturnsPost()
    {
        $req = $this->makeRequest(['_method' => 'CONNECT']);
        $this->assertSame('POST', $req->method());
    }

    public function testPostWithoutMethodFieldReturnsPost()
    {
        $req = $this->makeRequest([]);
        $this->assertSame('POST', $req->method());
    }

    // -------------------------------------------------------------------------
    // GET requests must never be spoofed
    // -------------------------------------------------------------------------

    public function testGetRequestIsNotSpoofed()
    {
        $req = $this->makeRequest(
            ['_method' => 'DELETE'],
            ['REQUEST_METHOD' => 'GET']
        );
        $this->assertSame('GET', $req->method());
    }

    // -------------------------------------------------------------------------
    // VerifyCsrfToken::isReading() with method spoofing
    // -------------------------------------------------------------------------

    public function testCsrfIsReadingReturnsFalseForSpoofedPut()
    {
        $middleware = new \Teguh02\Rijanphp\Core\Middleware\VerifyCsrfToken();
        $ref = new \ReflectionClass($middleware);
        $method = $ref->getMethod('isReading');
        $method->setAccessible(true);

        $request = [
            'server' => ['REQUEST_METHOD' => 'POST'],
            'input'  => ['_method' => 'PUT'],
        ];

        $this->assertFalse($method->invoke($middleware, $request));
    }

    public function testCsrfIsReadingReturnsFalseForSpoofedDelete()
    {
        $middleware = new \Teguh02\Rijanphp\Core\Middleware\VerifyCsrfToken();
        $ref = new \ReflectionClass($middleware);
        $method = $ref->getMethod('isReading');
        $method->setAccessible(true);

        $request = [
            'server' => ['REQUEST_METHOD' => 'POST'],
            'input'  => ['_method' => 'DELETE'],
        ];

        $this->assertFalse($method->invoke($middleware, $request));
    }

    public function testCsrfIsReadingReturnsTrueForGet()
    {
        $middleware = new \Teguh02\Rijanphp\Core\Middleware\VerifyCsrfToken();
        $ref = new \ReflectionClass($middleware);
        $method = $ref->getMethod('isReading');
        $method->setAccessible(true);

        $request = [
            'server' => ['REQUEST_METHOD' => 'GET'],
            'input'  => [],
        ];

        $this->assertTrue($method->invoke($middleware, $request));
    }

    public function testCsrfIsReadingReturnsTrueForPlainPostWithoutSpoof()
    {
        $middleware = new \Teguh02\Rijanphp\Core\Middleware\VerifyCsrfToken();
        $ref = new \ReflectionClass($middleware);
        $method = $ref->getMethod('isReading');
        $method->setAccessible(true);

        $request = [
            'server' => ['REQUEST_METHOD' => 'POST'],
            'input'  => [],
        ];

        $this->assertFalse($method->invoke($middleware, $request));
    }
}
