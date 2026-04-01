<?php
use Teguh02\Rijanphp\Core\Router\Router;
use Teguh02\Rijanphp\Modules\Product\Controllers\ProductController;

Router::group(['prefix' => 'products'], function() {
    Router::get('/', [ProductController::class, 'index'])->name('products.index');
    Router::get('/create', [ProductController::class, 'create'])->name('products.create');
    Router::post('/', [ProductController::class, 'store'])->name('products.store');
    Router::get('/{id}', [ProductController::class, 'show'])->name('products.show');
    Router::get('/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Router::put('/{id}', [ProductController::class, 'update'])->name('products.update');
    Router::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Router::get('/search', [ProductController::class, 'search'])->name('products.search');
    Router::get('/category/{category}', [ProductController::class, 'byCategory'])->name('products.category');
});
