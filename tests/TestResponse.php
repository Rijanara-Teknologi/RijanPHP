<?php

namespace Teguh02\Rijanphp\Tests;

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

    public function assertSee($text)
    {
        if (strpos($this->content, $text) === false) {
            throw new \PHPUnit\Framework\ExpectationFailedException(
                "Failed asserting that response contains \"$text\"."
            );
        }
    }
}
