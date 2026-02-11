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
