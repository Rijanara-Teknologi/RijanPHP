<?php
namespace Teguh02\Rijanphp\Core\Modules;

class Register
{
    public static $currentNamespace = null;

    /**
     * Initialize module components
     */
    public static function init(
        string $routes,
        string $views,
        string $models,
        string $controllers,
        ?string $helpers = null,
        ?string $namespace = null,
    ) {
        if ($namespace === null) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
            $callerFile = $backtrace[0]['file'];
            $namespace = strtolower(basename(dirname($callerFile)));
        }

        // Debug namespace detection
        error_log("Register::init - Namespace: " . ($namespace ?? 'NULL') . " for caller: " . ($callerFile ?? 'UNKNOWN'));

        self::$currentNamespace = $namespace;

        // Load Routes
        if ($routes && file_exists($routes . '/web.php')) {
            require $routes . '/web.php';
        }

        // Register Views
        if ($views && is_dir($views)) {
            if ($namespace) {
                \Teguh02\Rijanphp\Core\View\View::addNamespace($namespace, $views);
            } else {
                \Teguh02\Rijanphp\Core\View\View::addPath($views);
            }
        }

        self::$currentNamespace = null;
        return true;
    }
}