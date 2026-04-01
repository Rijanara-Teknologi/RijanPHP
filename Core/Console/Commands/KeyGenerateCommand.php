<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

class KeyGenerateCommand
{
    protected $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function handle(array $args)
    {
        $envPath = $this->basePath . '.env';

        if (!file_exists($envPath)) {
            echo "\033[31mError:\033[0m .env file not found.\n";
            exit(1);
        }

        $key = 'base64:' . base64_encode(random_bytes(32));

        $content = file_get_contents($envPath);

        if (strpos($content, 'APP_KEY=') !== false) {
            $content = preg_replace('/^APP_KEY=.*/m', 'APP_KEY=' . $key, $content);
        } else {
            $content .= "\nAPP_KEY=" . $key . "\n";
        }

        file_put_contents($envPath, $content);

        echo "\n\033[32mApplication key generated successfully!\033[0m\n";
        echo "Key: {$key}\n\n";
    }
}
