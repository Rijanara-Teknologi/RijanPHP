<?php

namespace Teguh02\Rijanphp\Core\Http;

class ClientResponse
{
    protected $statusCode;
    protected $body;
    protected $headers;

    public function __construct(int $statusCode, string $body, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }

    /**
     * Get the HTTP status code.
     */
    public function status(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the response body.
     */
    public function body(): string
    {
        return $this->body;
    }

    /**
     * Parse body as JSON.
     */
    public function json()
    {
        return json_decode($this->body, true);
    }

    /**
     * Get response headers.
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Get a specific header.
     */
    public function header(string $name)
    {
        $name = strtolower($name);
        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === $name) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Determine if the request was successful.
     */
    public function successful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Determine if the request was a server error.
     */
    public function serverError(): bool
    {
        return $this->statusCode >= 500;
    }

    /**
     * Determine if the request was a client error.
     */
    public function clientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }
}
