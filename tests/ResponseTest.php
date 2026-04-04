<?php

namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Http\Response;

/**
 * Tests for Bug #5: Response object missing __toString() method.
 *
 * When a controller returns a Response object that is echo'd, PHP must be able
 * to convert it to a string. Without __toString(), PHP throws a fatal error.
 */
class ResponseTest extends TestCase
{
    // -------------------------------------------------------------------------
    // __toString() presence and behaviour
    // -------------------------------------------------------------------------

    public function testToStringMethodExists()
    {
        $this->assertTrue(
            method_exists(Response::class, '__toString'),
            'Response must implement __toString()'
        );
    }

    public function testToStringReturnsContent()
    {
        $response = new Response();
        $response->setContent('Hello World');

        $this->assertSame('Hello World', (string) $response);
    }

    public function testToStringWithJsonContent()
    {
        $response = new Response();
        $response->json(['status' => 'ok', 'code' => 200]);

        $decoded = json_decode((string) $response, true);
        $this->assertSame('ok', $decoded['status']);
        $this->assertSame(200, $decoded['code']);
    }

    public function testToStringOnEmptyContentReturnsEmptyString()
    {
        $response = new Response();
        $this->assertSame('', (string) $response);
    }

    public function testEchoingResponseDoesNotThrow()
    {
        $response = new Response();
        $response->setContent('echo test');

        ob_start();
        echo $response;
        $output = ob_get_clean();

        $this->assertSame('echo test', $output);
    }

    // -------------------------------------------------------------------------
    // Existing send() / getters regression
    // -------------------------------------------------------------------------

    public function testStatusCode()
    {
        $response = (new Response())->status(404);
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testHeaderIsAdded()
    {
        $response = (new Response())->header('X-Custom', 'value');
        $this->assertSame('value', $response->getHeader('X-Custom'));
    }

    public function testJsonSetsContentTypeHeader()
    {
        $response = (new Response())->json(['key' => 'val']);
        $this->assertSame('application/json', $response->getHeader('Content-Type'));
    }

    public function testJsonSetsStatusCode()
    {
        $response = (new Response())->json([], 422);
        $this->assertSame(422, $response->getStatusCode());
    }

    public function testRedirectSetsLocationHeader()
    {
        $response = (new Response())->redirect('/dashboard');
        $this->assertSame('/dashboard', $response->getHeader('Location'));
    }

    public function testRedirectDefaultStatus()
    {
        $response = (new Response())->redirect('/home');
        $this->assertSame(302, $response->getStatusCode());
    }

    public function testRedirectCustomStatus()
    {
        $response = (new Response())->redirect('/home', 301);
        $this->assertSame(301, $response->getStatusCode());
    }

    public function testSetContentReturnsSelf()
    {
        $response = new Response();
        $returned = $response->setContent('test');
        $this->assertSame($response, $returned);
    }

    public function testGetContent()
    {
        $response = (new Response())->setContent('my content');
        $this->assertSame('my content', $response->getContent());
    }

    public function testGetHeaders()
    {
        $response = (new Response())
            ->header('A', '1')
            ->header('B', '2');
        $headers = $response->getHeaders();
        $this->assertArrayHasKey('A', $headers);
        $this->assertArrayHasKey('B', $headers);
    }

    // -------------------------------------------------------------------------
    // Fluent chaining
    // -------------------------------------------------------------------------

    public function testFluentChaining()
    {
        $response = (new Response())
            ->status(201)
            ->header('X-App', 'rijan')
            ->setContent('created');

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('rijan', $response->getHeader('X-App'));
        $this->assertSame('created', (string) $response);
    }
}
