<?php

use Teguh02\Rijanphp\Core\Cookie\Cookie;

if (!function_exists('cookie')) {
    function cookie($key = null, $default = null)
    {
        if (is_null($key)) {
            return new Cookie();
        }

        return Cookie::get($key, $default);
    }
}
