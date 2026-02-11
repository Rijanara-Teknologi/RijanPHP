<?php

use Teguh02\Rijanphp\Core\Session\Session;

if (!function_exists('session')) {
    function session($key = null, $default = null)
    {
        if (is_null($key)) {
            return new Session(); // Or return the class name/instance if we want method chaining
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                Session::set($k, $v);
            }
            return;
        }

        return Session::get($key, $default);
    }
}
