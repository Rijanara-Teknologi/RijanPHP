<?php

namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\Assert;

class TestResponse
{
    protected $content;
    protected $statusCode;

    public function __construct($content, $statusCode)
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
    }

    public function getBody()
    {
        return $this->content;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function assertStatus($status)
    {
        Assert::assertEquals($status, $this->statusCode, "Expected status code {$status} but received {$this->statusCode}.\nResponse Body: " . $this->content);
    }

    public function assertSee($text)
    {
        Assert::assertStringContainsString($text, $this->content, "Failed asserting that response contains \"$text\".");
    }
}
