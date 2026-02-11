<?php
namespace Teguh02\Rijanphp\Modules\Product;

class Product
{
    public function __construct()
    {
        \Teguh02\Rijanphp\Core\Modules\Register::init(
            routes: __DIR__ . '/Routes',
            views: __DIR__ . '/Views',
            models: __DIR__ . '/Models',
            controllers: __DIR__ . '/Controllers',
        );
    }
}
