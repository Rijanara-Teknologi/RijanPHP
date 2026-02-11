<?php defined('RIJANPHP') or die('Access denied.');

return [
    'name' => env('APP_NAME', 'RijanPHP'),
    'env' => env('APP_ENV', 'local'),
    'debug' => env('APP_DEBUG', true),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'Asia/Jakarta',
    'locale' => 'id',
    'key' => env('APP_KEY', ''),
    'cipher' => 'AES-256-CBC',
];
