<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

use Teguh02\Rijanphp\Core\Rijan;

class AboutCommand
{
    protected $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function handle(array $args)
    {
        $version = Rijan::version();
        $phpVersion = PHP_VERSION;
        $env = env('APP_ENV', 'production');
        $debug = env('APP_DEBUG', 'false');
        $dbConnection = env('DB_CONNECTION', 'sqlite');
        $timezone = config('app.timezone') ?? 'UTC';

        echo "\n";
        echo "\033[32m ╔══════════════════════════════════════════╗\033[0m\n";
        echo "\033[32m ║\033[0m        \033[1mRijanPHP Framework\033[0m              \033[32m║\033[0m\n";
        echo "\033[32m ╚══════════════════════════════════════════╝\033[0m\n";
        echo "\n";

        echo "  \033[1mEnvironment:\033[0m\n";
        echo "    Application Name ....... " . (config('app.name') ?? 'RijanPHP') . "\n";
        echo "    Version ................ \033[36m{$version}\033[0m\n";
        echo "    PHP Version ............ {$phpVersion}\n";
        echo "    Environment ............ \033[33m{$env}\033[0m\n";
        echo "    Debug Mode ............. " . ($debug === 'true' ? "\033[32mON\033[0m" : "\033[31mOFF\033[0m") . "\n";
        echo "    Timezone ............... {$timezone}\n";
        echo "\n";

        echo "  \033[1mDatabase:\033[0m\n";
        echo "    Connection ............. \033[36m{$dbConnection}\033[0m\n";
        echo "\n";

        echo "  \033[1mPaths:\033[0m\n";
        echo "    Base Path .............. {$this->basePath}\n";
        echo "    Config Path ............ " . $this->basePath . "config\n";
        echo "    Modules Path ........... " . $this->basePath . "Modules\n";
        echo "    Storage Path ........... " . $this->basePath . "storage\n";
        echo "\n";
    }
}
