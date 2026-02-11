<?php
namespace Teguh02\Rijanphp\Modules\Homepage;

class HomePage
{
    public function __construct()
    {
        \Teguh02\Rijanphp\Core\Modules\Register::init(
            views: __DIR__ . '/Views',
            routes: __DIR__ . '/Routes',
            models: __DIR__ . '/Models',
            controllers: __DIR__ . '/Controllers',
        );
    }
}