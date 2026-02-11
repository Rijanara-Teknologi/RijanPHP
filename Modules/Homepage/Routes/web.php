<?php

use Rijanphp\Modules\Homepage\Controllers\IndexController;
use Teguh02\Rijanphp\Core\Router\Router;

Router::get('/', [IndexController::class, 'index'])->name('home');

Router::group(['prefix' => 'example'], function () {
    Router::post('json', [IndexController::class, 'postExample'])->name('example.json');
});