<?php
namespace Teguh02\Rijanphp\Core\Modules;

class Module
{
    public static function load(...$modules): array
    {
        return $modules;
    }
}