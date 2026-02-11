<?php
namespace Teguh02\Rijanphp\Tests\Unit\Http;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Core\Http\Response;

class ResponseTest extends TestCase
{
    public function testSetAndGetContent(): void
    {
        $response = new Response();
        $response->setContent('Hello World');
        $this->assertEquals('Hello World', $response->getContent());
    }

    public function testSetAndGetStatusCode(): void
    {
        $response = new Response();
        $response->status(404);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testSetAndGetHeaders(): void
    {
        $response = new Response();
        $response->header('Content-Type', 'application/json');
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
    }

    public function testJsonResponse(): void
    {
        $data = ['name' => 'John'];
        $response = new Response();
        $response->json($data, 201);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $this->assertEquals(json_encode($data), $response->getContent());
    }
}
