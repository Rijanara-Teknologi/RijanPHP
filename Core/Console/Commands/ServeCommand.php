<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

class ServeCommand
{
    protected $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function handle(array $args)
    {
        $host = '127.0.0.1';
        $port = $this->getOption($args, '--port', '8000');

        echo "\n";
        echo "\033[32m ╔══════════════════════════════════════════╗\033[0m\n";
        echo "\033[32m ║\033[0m     \033[1mRijanPHP Development Server\033[0m        \033[32m║\033[0m\n";
        echo "\033[32m ╚══════════════════════════════════════════╝\033[0m\n";
        echo "\n";
        echo "  Server running at: \033[36mhttp://{$host}:{$port}\033[0m\n";
        echo "  Document root:     \033[33m{$this->basePath}\033[0m\n";
        echo "  PHP version:       \033[36m" . PHP_VERSION . "\033[0m\n";
        echo "\n";
        echo "  Press \033[31mCtrl+C\033[0m to stop the server.\n\n";

        passthru('php -S ' . $host . ':' . $port . ' -t ' . escapeshellarg($this->basePath));
    }

    protected function getOption(array $args, string $option, $default = null)
    {
        foreach ($args as $arg) {
            if (strpos($arg, $option . '=') === 0) {
                return substr($arg, strlen($option . '='));
            }
        }
        return $default;
    }
}
