<?php

use Teguh02\Rijanphp\Core\View\View;

if (!function_exists('view')) {
    function view($view, $data = [])
    {
        return View::render($view, $data);
    }
}

if (!function_exists('extends_layout')) {
    function extends_layout($layout)
    {
        View:: extends($layout);
    }
}

if (!function_exists('section')) {
    function section($name)
    {
        View::section($name);
    }
}

if (!function_exists('end_section')) {
    function end_section()
    {
        View::endSection();
    }
}

if (!function_exists('yield_section')) {
    function yield_section($name, $default = '')
    {
        return View::yield($name, $default);
    }
}

if (!function_exists('include_view')) {
    function include_view($view, $data = [])
    {
        return View::render($view, $data);
    }
}
