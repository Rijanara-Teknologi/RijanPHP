<?php
use Teguh02\Rijanphp\Core\Router\Router;
use Teguh\Rijanphp\Modules\Blog\Controllers\BlogController;

Router::group(['prefix' => strtolower('Blog')], function() {
    Router::get('/', [BlogController::class, 'index'])->name('Blog.index');
});