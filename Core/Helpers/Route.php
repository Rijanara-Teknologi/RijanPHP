<?php

use Teguh02\Rijanphp\Core\Router\Router;

if (!function_exists('route')) {
    function route($name, $params = [])
    {
        return Router::route($name, $params);
    }
}
