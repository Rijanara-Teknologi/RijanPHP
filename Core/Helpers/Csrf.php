<?php

use Teguh02\Rijanphp\Core\Security\Csrf;

if (!function_exists('csrf_token')) {
    /**
     * Get the current CSRF token.
     *
     * @return string
     */
    function csrf_token()
    {
        return Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate a hidden CSRF token input field.
     *
     * @return string
     */
    function csrf_field()
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('csrf_meta')) {
    /**
     * Generate a CSRF token meta tag.
     *
     * @return string
     */
    function csrf_meta()
    {
        return '<meta name="csrf-token" content="' . csrf_token() . '">';
    }
}
