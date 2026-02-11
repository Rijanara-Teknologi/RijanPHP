<?php

use Teguh02\Rijanphp\Core\Http\Request;
use Teguh02\Rijanphp\Core\Http\Response;

if (!function_exists('request')) {
    /**
     * Get the request instance or a specific input.
     */
    function request($key = null, $default = null)
    {
        $instance = Request::$instance ?? new Request();

        if (is_null($key)) {
            return $instance;
        }

        return $instance->input($key, $default);
    }
}

if (!function_exists('response')) {
    /**
     * Create a new response instance.
     */
    function response()
    {
        return new Response();
    }
}

if (!function_exists('redirect')) {
    /**
     * Create a redirect response.
     */
    function redirect(string $url, int $status = 302)
    {
        return (new Response())->redirect($url, $status);
    }
}

if (!function_exists('back')) {
    /**
     * Redirect back to the previous page.
     */
    function back(int $status = 302)
    {
        $url = request()->server('HTTP_REFERER', '/');
        return redirect($url, $status);
    }
}

if (!function_exists('http')) {
    /**
     * Create a new HTTP Client instance.
     */
    function http(array $options = [])
    {
        return new \Teguh02\Rijanphp\Core\Http\Client($options);
    }
}
