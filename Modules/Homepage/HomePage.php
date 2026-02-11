<?php
namespace Teguh02\Rijanphp\Modules\Homepage;

class HomePage
{
    public function __construct()
    {
        \Teguh02\Rijanphp\Core\Modules\Register::init(
            views: __DIR__ . '/views',
            routes: __DIR__ . '/routes',
            models: __DIR__ . '/models',
            controllers: __DIR__ . '/controllers',
        );
    }
}