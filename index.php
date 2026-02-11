<?php define('RIJANPHP', true);

file_exists(__DIR__ . '/vendor/autoload.php') ? 
    require __DIR__ . '/vendor/autoload.php' : 
    require __DIR__ . '/Core/Autoload/Autoloader.php';

return (new \Teguh02\Rijanphp\Core\Rijan())
    ->base_path(__DIR__)
    ->config(__DIR__ . '/config')
    ->request($_REQUEST)
    ->cookie($_COOKIE)
    ->session($_SESSION ?? [])
    ->server($_SERVER)
    ->env($_ENV)
    ->run();