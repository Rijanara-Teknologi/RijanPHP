<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

class DbSeedCommand
{
    protected $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function handle(array $args)
    {
        echo "\n\033[1mDatabase Seeding...\033[0m\n\n";

        $seederClass = $this->getOption($args, '--class');

        if ($seederClass) {
            $this->runSeeder($seederClass);
        } else {
            $this->runDefaultSeeder();
        }
    }

    protected function runDefaultSeeder(): void
    {
        $path = $this->basePath . 'Master/Seeds/DatabaseSeeder.php';

        if (!file_exists($path)) {
            echo "\033[33mNo DatabaseSeeder found. Use --class=SeederName to run a specific seeder.\033[0m\n";
            return;
        }

        require_once $path;
        $seeder = new \Teguh02\Rijanphp\Master\Seeds\DatabaseSeeder();
        $seeder->run();

        echo "\033[32mDatabase seeded successfully!\033[0m\n";
    }

    protected function runSeeder(string $className): void
    {
        $paths = [
            $this->basePath . 'Master/Seeds/' . $className . '.php',
        ];

        $modulesDir = $this->basePath . 'Modules';
        if (is_dir($modulesDir)) {
            foreach (scandir($modulesDir) as $module) {
                if ($module === '.' || $module === '..') continue;
                $paths[] = $modulesDir . '/' . $module . '/Seeds/' . $className . '.php';
            }
        }

        $found = false;
        foreach ($paths as $path) {
            if (file_exists($path)) {
                require_once $path;
                $fullClass = $this->resolveFullClassName($className, $path);
                if (class_exists($fullClass)) {
                    $seeder = new $fullClass();
                    $seeder->run();
                    echo "\033[32mSeeder '{$className}' executed successfully!\033[0m\n";
                    $found = true;
                }
                break;
            }
        }

        if (!$found) {
            echo "\033[31mError:\033[0m Seeder '{$className}' not found.\n";
            exit(1);
        }
    }

    protected function resolveFullClassName(string $className, string $path): string
    {
        if (strpos($path, '/Master/') !== false) {
            return "\\Teguh02\\Rijanphp\\Master\\Seeds\\{$className}";
        }

        if (strpos($path, '/Modules/') !== false) {
            preg_match('#/Modules/([^/]+)/#', $path, $matches);
            $module = $matches[1] ?? '';
            return "\\Teguh02\\Rijanphp\\Modules\\{$module}\\Seeds\\{$className}";
        }

        return $className;
    }

    protected function getOption(array $args, string $option)
    {
        foreach ($args as $arg) {
            if (strpos($arg, $option . '=') === 0) {
                return substr($arg, strlen($option . '='));
            }
        }
        return null;
    }
}
