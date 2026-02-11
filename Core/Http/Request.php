<?php

namespace Teguh02\Rijanphp\Core\Http;

class Request
{
    public static $instance;
    protected $get;
    protected $post;
    protected $files;
    protected $server;
    protected $json;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;

        $content = file_get_contents('php://input');
        $this->json = json_decode($content, true) ?? [];
    }

    /**
     * Get all input data.
     */
    public function all()
    {
        return array_merge($this->get, $this->post, $this->json);
    }

    /**
     * Get a specific input value.
     */
    public function input($key, $default = null)
    {
        $data = $this->all();
        return $data[$key] ?? $default;
    }

    /**
     * Get query parameter.
     */
    public function query($key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    /**
     * Get post parameter.
     */
    public function post($key, $default = null)
    {
        return $this->post[$key] ?? $this->json[$key] ?? $default;
    }

    /**
     * Get file data.
     */
    public function file($key)
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Get the request method.
     */
    public function method()
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Get the request URI.
     */
    public function uri()
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        return parse_url($uri, PHP_URL_PATH);
    }

    /**
     * Check if request is AJAX.
     */
    public function isAjax()
    {
        return isset($this->server['HTTP_X_REQUESTED_WITH']) &&
            strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get a subset of the input data.
     */
    public function only(array $keys)
    {
        $results = [];
        $data = $this->all();

        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                $results[$key] = $data[$key];
            }
        }

        return $results;
    }

    /**
     * Get all input data except for a specified array of keys.
     */
    public function except(array $keys)
    {
        $data = $this->all();

        foreach ($keys as $key) {
            unset($data[$key]);
        }

        return $data;
    }

    /**
     * Determine if the request contains a given input item.
     */
    public function has($key)
    {
        $data = $this->all();
        return array_key_exists($key, $data);
    }

    /**
     * Determine if the request is of a certain method.
     */
    public function isMethod($method)
    {
        return strtoupper($method) === $this->method();
    }

    /**
     * Get a header from the request.
     */
    public function header($key, $default = null)
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $this->server[$key] ?? $this->server[str_replace('HTTP_', '', $key)] ?? $default;
    }

    /**
     * Get the client's IP address.
     */
    public function ip()
    {
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Get the client's User Agent.
     */
    public function userAgent()
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Get the bearer token from the request headers.
     */
    public function bearerToken()
    {
        $header = $this->header('Authorization');
        if (strpos($header, 'Bearer ') === 0) {
            return substr($header, 7);
        }
        return null;
    }

    /**
     * Get a cookie from the request.
     */
    public function cookie($key, $default = null)
    {
        return $_COOKIE[$key] ?? $default;
    }

    /**
     * Determine if the request is secure (HTTPS).
     */
    public function secure()
    {
        return isset($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off';
    }

    /**
     * Get the request path.
     */
    public function path()
    {
        return $this->uri();
    }

    /**
     * Get the full URL for the request.
     */
    public function url()
    {
        $scheme = $this->secure() ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host . $this->uri();
    }
}
