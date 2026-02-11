<?php

if (!function_exists('env')) {
    /**
     * Get the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if (!function_exists('config')) {
    /**
     * Get the specified configuration value.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function config($key, $default = null)
    {
        // Simple config loader
        // config('app.name') -> maps to config/app.php ['name']
        // config('database') -> maps to config/database.php (entire array)

        $parts = explode('.', $key);
        $file = array_shift($parts);

        $path = \Teguh02\Rijanphp\Core\Rijan::$instance->base_path('config/' . $file . '.php');

        if (!file_exists($path)) {
            return $default;
        }

        $config = require $path;

        if (empty($parts)) {
            return $config;
        }

        foreach ($parts as $part) {
            if (isset($config[$part])) {
                $config = $config[$part];
            } else {
                return $default;
            }
        }

        return $config;
    }
}
