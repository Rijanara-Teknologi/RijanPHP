<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

class CacheClearCommand
{
    protected $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function handle(array $args)
    {
        echo "\n\033[1mClearing Cache...\033[0m\n\n";

        $dirs = [
            'storage/framework/cache',
            'storage/logs',
        ];

        foreach ($dirs as $dir) {
            $path = $this->basePath . $dir;
            if (is_dir($path)) {
                $this->clearDirectory($path);
                echo "\033[32mCleared:\033[0m {$dir}\n";
            }
        }

        echo "\n\033[32mCache cleared successfully!\033[0m\n";
    }

    protected function clearDirectory(string $dir): void
    {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->clearDirectory($file);
                rmdir($file);
            } else {
                unlink($file);
            }
        }
    }
}
