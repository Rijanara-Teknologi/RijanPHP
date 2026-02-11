<?php
namespace Teguh02\Rijanphp\Core\Modules;

class Register
{
    /**
     * Initialize module components
     */
    public static function init(
        string $routes,
        string $views,
        string $models,
        string $controllers,
        ?string $helpers = null,
        ?string $middleware = null,
    ) {
        // Load Routes
        if ($routes && file_exists($routes . '/web.php')) {
            // echo "Loading routes from: " . $routes . '/web.php' . "\n";
            require_once $routes . '/web.php';
        } else {
            // echo "No routes found for: " . $routes . "\n";
        }

        // Register Views
        if ($views && is_dir($views)) {
            \Teguh02\Rijanphp\Core\View\View::addPath($views);
        }

        return true;
    }
}