<?php
namespace Teguh02\Rijanphp\Core\Modules;

class Executor
{
    public $config_path;

    public static function Run()
    {
        // Load modules configuration to bootstrap them
        config('modules');

        echo \Teguh02\Rijanphp\Core\Router\Router::dispatch();
    }
}