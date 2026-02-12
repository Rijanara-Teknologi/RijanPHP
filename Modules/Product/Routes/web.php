<?php
use Teguh02\Rijanphp\Core\Router\Router;
use Teguh02\Rijanphp\Modules\Product\Controllers\ProductController;

Router::get('/products', [ProductController::class, 'index']);
Router::get('/product/id/{id}', [ProductController::class, 'show']);
Router::get('/product/slug/{slug}', [ProductController::class, 'showBySlug']);

Router::post('/products', [ProductController::class, 'store']);
Router::put('/product/id/{id}', [ProductController::class, 'update']);
Router::delete('/product/id/{id}', [ProductController::class, 'destroy']);

Router::get('/test-master-layout', function () {
    return view('test_layout');
});
