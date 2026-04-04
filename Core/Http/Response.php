<?php

namespace Teguh02\Rijanphp\Core\Http;

class Response
{
    protected $statusCode = 200;
    protected $headers = [];
    protected $content = '';

    /**
     * Set the status code.
     */
    public function status(int $code)
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Add a header.
     */
    public function header(string $name, string $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Set JSON content.
     */
    public function json($data, int $status = 200)
    {
        $this->status($status);
        $this->header('Content-Type', 'application/json');
        $this->content = json_encode($data);
        return $this;
    }

    /**
     * Send a redirect response.
     */
    public function redirect(string $url, int $status = 302)
    {
        $this->status($status);
        $this->header('Location', $url);
        return $this;
    }

    /**
     * Set HTML content.
     */
    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Send the response to the client.
     */
    public function send()
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }

        echo $this->content;
    }

    /**
     * Get the status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Get the content.
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get all headers.
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get a specific header.
     */
    public function getHeader($name)
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * Convert the response to string — sends headers and returns content.
     * Called automatically when the object is cast/echoed as a string.
     */
    public function __toString()
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }

        return (string) $this->content;
    }
}
