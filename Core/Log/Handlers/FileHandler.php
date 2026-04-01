<?php

namespace Teguh02\Rijanphp\Core\Log\Handlers;

use Teguh02\Rijanphp\Core\Log\Handler;

class FileHandler implements Handler
{
    protected $path;
    protected $maxSize;
    protected $maxFiles;

    public function __construct($path, $maxSize = null, $maxFiles = null)
    {
        $this->path = $path;
        $this->maxSize = $maxSize ?: 10 * 1024 * 1024;
        $this->maxFiles = $maxFiles ?: 5;
    }

    public function write($level, $message, array $context = [])
    {
        $directory = dirname($this->path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($this->path) && filesize($this->path) >= $this->maxSize) {
            $this->rotate();
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextJson = !empty($context) ? json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '';
        $logLine = "[{$timestamp}] {$level}: {$message} {$contextJson}" . PHP_EOL;

        $fp = fopen($this->path, 'a');
        if ($fp) {
            flock($fp, LOCK_EX);
            fwrite($fp, $logLine);
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    protected function rotate()
    {
        if (!file_exists($this->path)) {
            return;
        }

        for ($i = $this->maxFiles - 1; $i >= 1; $i--) {
            $oldFile = $this->path . '.' . $i;
            $newFile = $this->path . '.' . ($i + 1);

            if (file_exists($oldFile)) {
                if ($i + 1 > $this->maxFiles) {
                    unlink($oldFile);
                } else {
                    rename($oldFile, $newFile);
                }
            }
        }

        rename($this->path, $this->path . '.1');
    }
}
