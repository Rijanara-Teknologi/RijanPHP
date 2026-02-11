<?php

use Teguh02\Rijanphp\Core\Storage\Storage;

if (!function_exists('storage')) {
    /**
     * Get the storage instance.
     */
    function storage(?string $disk = null)
    {
        static $instance;

        if (!$instance) {
            $basePath = defined('BASE_PATH') ? BASE_PATH : getcwd();
            $instance = new Storage($basePath);
        }

        return $instance->disk($disk);
    }
}
