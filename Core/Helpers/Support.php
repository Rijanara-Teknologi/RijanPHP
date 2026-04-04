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

        $path = base_path('config/' . $file . '.php');

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

if (!function_exists('base_path')) {
    /**
     * Get the horizontal base path.
     *
     * @param  string  $path
     * @return string
     */
    function base_path($path = '')
    {
        return \Teguh02\Rijanphp\Core\Rijan::$instance->base_path($path);
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get the storage path.
     *
     * @param  string  $path
     * @return string
     */
    function storage_path($path = '')
    {
        return base_path('storage/' . $path);
    }
}

if (!function_exists('database_path')) {
    /**
     * Get the database path (storage/database).
     *
     * @param  string  $path
     * @return string
     */
    function database_path($path = '')
    {
        return base_path('storage/database/' . $path);
    }
}

if (!function_exists('public_path')) {
    /**
     * Get the public directory path.
     *
     * @param  string  $path
     * @return string
     */
    function public_path($path = '')
    {
        return base_path('public/' . $path);
    }
}

if (!function_exists('asset')) {
    /**
     * Generate a URL for a public asset.
     *
     * @param  string  $path
     * @return string
     */
    function asset($path = '')
    {
        // Return as-is if already an absolute URL
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $appUrl = rtrim(env('APP_URL', ''), '/');
        $path = ltrim($path, '/');

        return $appUrl . '/public/' . $path;
    }
}

if (!function_exists('url')) {
    /**
     * Generate a full URL for a given path.
     *
     * @param  string  $path
     * @return string
     */
    function url($path = '')
    {
        // Return as-is if already an absolute URL
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $appUrl = rtrim(env('APP_URL', ''), '/');
        $path = ltrim($path, '/');

        return $appUrl . '/' . $path;
    }
}
