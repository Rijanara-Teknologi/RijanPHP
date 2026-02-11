<?php

namespace Teguh02\Rijanphp\Core\Cookie;

class Cookie
{
    protected static $queued = [];

    public static function set($name, $value, $minutes = 0, $path = '/', $domain = '', $secure = false, $httpOnly = true)
    {
        $expire = ($minutes == 0) ? 0 : time() + ($minutes * 60);
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    public static function get($name, $default = null)
    {
        if (isset(\Teguh02\Rijanphp\Core\Rijan::$instance)) {
            return \Teguh02\Rijanphp\Core\Rijan::$instance->cookie[$name] ?? $default;
        }
        return $_COOKIE[$name] ?? $default;
    }

    public static function has($name)
    {
        if (isset(\Teguh02\Rijanphp\Core\Rijan::$instance)) {
            return isset(\Teguh02\Rijanphp\Core\Rijan::$instance->cookie[$name]);
        }
        return isset($_COOKIE[$name]);
    }

    public static function delete($name, $path = '/', $domain = '')
    {
        self::set($name, '', -1, $path, $domain);
        unset($_COOKIE[$name]);
    }

    public static function queue($name, $value, $minutes = 0, $path = '/', $domain = '', $secure = false, $httpOnly = true)
    {
        self::$queued[$name] = [
            'value' => $value,
            'minutes' => $minutes,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httpOnly' => $httpOnly
        ];
    }

    public static function sendQueuedCookies()
    {
        foreach (self::$queued as $name => $options) {
            self::set(
                $name,
                $options['value'],
                $options['minutes'],
                $options['path'],
                $options['domain'],
                $options['secure'],
                $options['httpOnly']
            );
        }
        self::$queued = [];
    }

    public static function getQueuedCookies()
    {
        return self::$queued;
    }

    public static function clearQueuedCookies()
    {
        self::$queued = [];
    }

    public static function forever($name, $value, $path = '/', $domain = '', $secure = false, $httpOnly = true)
    {
        self::set($name, $value, 2628000, $path, $domain, $secure, $httpOnly); // 5 years
    }

    public static function forget($name, $path = '/', $domain = '')
    {
        self::delete($name, $path, $domain);
    }
}
