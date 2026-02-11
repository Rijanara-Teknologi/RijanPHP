<?php

namespace Teguh02\Rijanphp\Core\Log\Handlers;

use Teguh02\Rijanphp\Core\Log\Handler;

class FileHandler implements Handler
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function write($level, $message, array $context = [])
    {
        $directory = dirname($this->path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextJson = !empty($context) ? json_encode($context) : '';
        $logLine = "[{$timestamp}] {$level}: {$message} {$contextJson}" . PHP_EOL;

        file_put_contents($this->path, $logLine, FILE_APPEND);
    }
}
