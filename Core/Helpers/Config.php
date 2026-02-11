<?php

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        $config_path = \Teguh02\Rijanphp\Core\Rijan::$instance->config_path;

        if (is_null($key)) {
            // Return all configurations? Or just return null/instance?
            // For now let's return null or maybe an empty array if we want to retrieve all configs (complex)
            // But usually config() without args often returns a config loader instance in some frameworks.
            // Let's stick to simple key retrieval.
            return [];
        }

        $keys = explode('.', $key);
        $file = array_shift($keys);

        $path = $config_path . $file . '.php';

        if (!file_exists($path)) {
            return $default;
        }

        $config = require $path;

        if (empty($keys)) {
            return $config;
        }

        foreach ($keys as $segment) {
            if (isset($config[$segment])) {
                $config = $config[$segment];
            } else {
                return $default;
            }
        }

        return $config;
    }
}