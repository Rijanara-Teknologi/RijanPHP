<?php

use Teguh02\Rijanphp\Core\Database\DatabaseManager;
use Teguh02\Rijanphp\Core\Database\QueryBuilder;

if (!function_exists('db')) {
    function db($connection = null)
    {
        $manager = DatabaseManager::getInstance();
        $conn = $manager->connection($connection);

        return new QueryBuilder($conn);
    }
}

if (!function_exists('table')) {
    function table($table, $connection = null)
    {
        return db($connection)->table($table);
    }
}
if (!function_exists('database_path')) {
    function database_path($path = '')
    {
        $basePath = \Teguh02\Rijanphp\Core\Rijan::$instance->base_path();
        return $basePath . 'storage' . DIRECTORY_SEPARATOR . 'database' . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}
