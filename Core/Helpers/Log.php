<?php

use Teguh02\Rijanphp\Core\Log\LogManager;

if (!function_exists('logger')) {
    /**
     * Log a debug message or get the logger instance.
     *
     * @param  string|null  $message
     * @param  array  $context
     * @return \Teguh02\Rijanphp\Core\Log\LogManager|\Teguh02\Rijanphp\Core\Log\Logger
     */
    function logger($message = null, array $context = [])
    {
        if (is_null($message)) {
            return LogManager::getInstance();
        }

        return LogManager::getInstance()->debug($message, $context);
    }
}

if (!function_exists('log_info')) {
    function log_info($message, array $context = [])
    {
        return LogManager::getInstance()->info($message, $context);
    }
}

if (!function_exists('log_error')) {
    function log_error($message, array $context = [])
    {
        return LogManager::getInstance()->error($message, $context);
    }
}

if (!function_exists('log_warning')) {
    function log_warning($message, array $context = [])
    {
        return LogManager::getInstance()->warning($message, $context);
    }
}

if (!function_exists('log_debug')) {
    function log_debug($message, array $context = [])
    {
        return LogManager::getInstance()->debug($message, $context);
    }
}

if (!function_exists('log_notice')) {
    function log_notice($message, array $context = [])
    {
        return LogManager::getInstance()->notice($message, $context);
    }
}

if (!function_exists('log_critical')) {
    function log_critical($message, array $context = [])
    {
        return LogManager::getInstance()->critical($message, $context);
    }
}

if (!function_exists('log_alert')) {
    function log_alert($message, array $context = [])
    {
        return LogManager::getInstance()->alert($message, $context);
    }
}

if (!function_exists('log_emergency')) {
    function log_emergency($message, array $context = [])
    {
        return LogManager::getInstance()->emergency($message, $context);
    }
}

if (!function_exists('log_request')) {
    /**
     * Log an HTTP request summary.
     *
     * @param  string  $method   HTTP verb (GET, POST, …)
     * @param  string  $url      Request URL
     * @param  string  $ip       Client IP address
     * @param  int|null $status  HTTP response status code
     */
    function log_request($method, $url, $ip, $status = null)
    {
        $context = ['ip' => $ip];
        if ($status !== null) {
            $context['status'] = $status;
        }
        return LogManager::getInstance()->info("HTTP {$method} {$url}", $context);
    }
}

if (!function_exists('log_query')) {
    /**
     * Log a SQL query for debugging.
     *
     * @param  string  $sql       The SQL statement
     * @param  array   $bindings  Bound parameter values
     * @param  float|null $time   Execution time in milliseconds
     */
    function log_query($sql, array $bindings = [], $time = null)
    {
        $context = ['bindings' => $bindings];
        if ($time !== null) {
            $context['time_ms'] = $time;
        }
        return LogManager::getInstance()->debug("SQL: {$sql}", $context);
    }
}
