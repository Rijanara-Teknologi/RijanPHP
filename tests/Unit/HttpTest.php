<?php

namespace Teguh02\Rijanphp\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Http\Request;
use Teguh02\Rijanphp\Core\Http\Response;

class HttpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Request::$instance = null;
    }

    public function testRequestInstance()
    {
        $request = new Request();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestMethod()
    {
        $request = new Request();
        $this->assertEquals('GET', $request->method());
    }

    public function testRequestUri()
    {
        $request = new Request();
        $this->assertIsString($request->uri());
    }

    public function testRequestInputWithDefault()
    {
        $request = new Request();
        $this->assertEquals('default', $request->input('nonexistent', 'default'));
    }

    public function testRequestQuery()
    {
        $request = new Request();
        $this->assertNull($request->query('nonexistent'));
    }

    public function testRequestOnly()
    {
        $request = new Request();
        $result = $request->only(['name', 'email']);
        $this->assertIsArray($result);
    }

    public function testRequestExcept()
    {
        $request = new Request();
        $result = $request->except(['password']);
        $this->assertIsArray($result);
    }

    public function testRequestHas()
    {
        $request = new Request();
        $this->assertFalse($request->has('nonexistent_key'));
    }

    public function testRequestIsMethod()
    {
        $request = new Request();
        $this->assertTrue($request->isMethod('GET'));
        $this->assertFalse($request->isMethod('POST'));
    }

    public function testRequestIp()
    {
        $request = new Request();
        $this->assertIsString($request->ip());
    }

    public function testRequestUserAgent()
    {
        $request = new Request();
        $this->assertIsString($request->userAgent());
    }

    public function testRequestSecure()
    {
        $request = new Request();
        $this->assertIsBool($request->secure());
    }

    public function testRequestUrl()
    {
        $request = new Request();
        $this->assertIsString($request->url());
    }

    public function testResponseContent()
    {
        $response = new Response();
        $response->setContent('Hello World');

        $this->assertEquals('Hello World', $response->getContent());
    }

    public function testResponseStatus()
    {
        $response = new Response();
        $response->status(201);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testResponseHeader()
    {
        $response = new Response();
        $response->header('X-Custom', 'value');

        $this->assertEquals('value', $response->getHeader('X-Custom'));
    }

    public function testResponseJson()
    {
        $response = new Response();
        $response->json(['status' => 'ok']);

        $this->assertJson($response->getContent());
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
    }

    public function testResponseRedirect()
    {
        $response = new Response();
        $response->redirect('/dashboard');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/dashboard', $response->getHeader('Location'));
    }

    public function testResponseRedirectWithCustomStatus()
    {
        $response = new Response();
        $response->redirect('/new-url', 301);

        $this->assertEquals(301, $response->getStatusCode());
    }
}
