<?php

namespace Teguh02\Rijanphp\Core\Exception;

use Throwable;
use Teguh02\Rijanphp\Core\View\View;

class Handler
{
    /**
     * Register the error and exception handlers.
     */
    public static function register()
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Handle uncaught exceptions.
     */
    public static function handleException(Throwable $e)
    {
        self::report($e);
        self::render($e);
    }

    /**
     * Handle PHP errors.
     */
    public static function handleError($level, $message, $file = '', $line = 0)
    {
        if (error_reporting() & $level) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Handle fatal errors on shutdown.
     */
    public static function handleShutdown()
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            self::handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    /**
     * Report or log an exception.
     */
    protected static function report(Throwable $e)
    {
        if (function_exists('logger')) {
            logger()->error($e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Render an exception into an HTTP response.
     */
    protected static function render(Throwable $e)
    {
        $isDebug = env('APP_DEBUG', false);

        // Clear any existing output buffers to ensure a clean error page
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Clear view state to prevent layout/section pollution from crashed views
        View::clear();

        if (!headers_sent()) {
            http_response_code(500);
        }

        try {
            echo View::render('core::errors.500', [
                'exception' => $e,
                'debug' => $isDebug
            ]);
        } catch (\Exception $viewException) {
            // Fallback if view engine fails
            if ($isDebug) {
                echo "<h1>Internal Server Error</h1>";
                echo "<p>{$e->getMessage()}</p>";
                echo "<pre>{$e->getTraceAsString()}</pre>";
            } else {
                echo "<h1>Something went wrong on our end.</h1>";
            }
        }
        exit;
    }
}
