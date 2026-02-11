<?php

namespace Teguh02\Rijanphp\Tests\Feature;

use Teguh02\Rijanphp\Tests\TestCase;

class HttpClientTest extends TestCase
{
    public function test_get_request()
    {
        $response = http()->get('https://jsonplaceholder.typicode.com/posts/1');

        $this->assertEquals(200, $response->status());
        $this->assertTrue($response->successful());
        $this->assertIsArray($response->json());
        $this->assertEquals(1, $response->json()['id']);
    }

    public function test_post_json_request()
    {
        $response = http()->post('https://jsonplaceholder.typicode.com/posts', [
            'json' => [
                'title' => 'RijanPHP',
                'body' => 'Modern Framework',
                'userId' => 1,
            ]
        ]);

        $this->assertEquals(201, $response->status());
        $this->assertEquals('RijanPHP', $response->json()['title']);
    }

    public function test_client_error_handling()
    {
        $response = http()->get('https://jsonplaceholder.typicode.com/posts/999999');

        $this->assertEquals(404, $response->status());
        $this->assertTrue($response->clientError());
        $this->assertFalse($response->successful());
    }

    public function test_headers_handling()
    {
        $response = http()->get('https://jsonplaceholder.typicode.com/posts/1');

        $this->assertNotNull($response->header('Content-Type'));
        $this->assertStringContainsString('application/json', $response->header('Content-Type'));
    }
}
