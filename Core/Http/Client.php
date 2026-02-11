<?php

namespace Teguh02\Rijanphp\Core\Http;

class Client
{
    protected $baseUrl = '';
    protected $options = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->baseUrl = $options['base_uri'] ?? '';
    }

    /**
     * Send a GET request.
     */
    public function get(string $url, array $options = []): ClientResponse
    {
        return $this->request('GET', $url, $options);
    }

    /**
     * Send a POST request.
     */
    public function post(string $url, array $options = []): ClientResponse
    {
        return $this->request('POST', $url, $options);
    }

    /**
     * Send a PUT request.
     */
    public function put(string $url, array $options = []): ClientResponse
    {
        return $this->request('PUT', $url, $options);
    }

    /**
     * Send a PATCH request.
     */
    public function patch(string $url, array $options = []): ClientResponse
    {
        return $this->request('PATCH', $url, $options);
    }

    /**
     * Send a DELETE request.
     */
    public function delete(string $url, array $options = []): ClientResponse
    {
        return $this->request('DELETE', $url, $options);
    }

    /**
     * Send an HTTP request.
     */
    public function request(string $method, string $url, array $options = []): ClientResponse
    {
        $options = array_merge($this->options, $options);
        $fullUrl = $this->buildUrl($url, $options['query'] ?? []);

        $ch = curl_init($fullUrl);

        $headers = $this->prepareHeaders($options);
        $method = strtoupper($method);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (isset($options['timeout'])) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $options['timeout']);
        }

        if (isset($options['auth'])) {
            curl_setopt($ch, CURLOPT_USERPWD, implode(':', $options['auth']));
        }

        $this->preparePayload($ch, $method, $options, $headers);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        $responseHeaders = $this->parseHeaders(substr($response, 0, $headerSize));
        $body = substr($response, $headerSize);

        return new ClientResponse($statusCode, $body, $responseHeaders);
    }

    protected function buildUrl(string $url, array $query = []): string
    {
        if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
            $url = rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/');
        }

        if (!empty($query)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($query);
        }

        return $url;
    }

    protected function prepareHeaders(array &$options): array
    {
        $headers = $options['headers'] ?? [];
        $prepared = [];

        foreach ($headers as $key => $value) {
            $prepared[] = "{$key}: {$value}";
        }

        return $prepared;
    }

    protected function preparePayload($ch, string $method, array $options, array &$headers)
    {
        if ($method === 'GET') {
            return;
        }

        if (isset($options['json'])) {
            $payload = json_encode($options['json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        } elseif (isset($options['form_params'])) {
            $payload = http_build_query($options['form_params']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        } elseif (isset($options['body'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['body']);
        }
    }

    protected function parseHeaders(string $headerContent): array
    {
        $headers = [];
        $lines = explode("\r\n", trim($headerContent));

        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $headers[trim($key)] = trim($value);
            }
        }

        return $headers;
    }
}
