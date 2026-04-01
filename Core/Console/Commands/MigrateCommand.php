<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

use Teguh02\Rijanphp\Core\Database\Migration\Migrator;
use Teguh02\Rijanphp\Core\Database\DatabaseManager;

class MigrateCommand
{
    protected $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function handle(array $args)
    {
        echo "\n\033[1mRunning Migrations...\033[0m\n\n";

        $paths = $this->getMigrationPaths($args);

        if (empty($paths)) {
            echo "\033[33mNo migration paths specified.\033[0m\n";
            echo "Usage: php rijan migrate [--path=Modules/Blog/Migrations]\n";
            exit(1);
        }

        $migrator = new Migrator();

        try {
            $migrator->install();
            $migrated = $migrator->run($paths);

            if (empty($migrated)) {
                echo "\033[33mNothing to migrate.\033[0m\n";
            } else {
                foreach ($migrated as $migration) {
                    echo "\033[32mMigrated:\033[0m {$migration}\n";
                }
                echo "\n\033[32mSuccessfully migrated " . count($migrated) . " migration(s)!\033[0m\n";
            }
        } catch (\Exception $e) {
            echo "\033[31mMigration failed:\033[0m " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    protected function getMigrationPaths(array $args): array
    {
        $paths = [];

        foreach ($args as $arg) {
            if (strpos($arg, '--path=') === 0) {
                $paths[] = substr($arg, 7);
            }
        }

        if (empty($paths)) {
            $paths[] = $this->basePath . 'Master/Migrations';

            $modulesDir = $this->basePath . 'Modules';
            if (is_dir($modulesDir)) {
                foreach (scandir($modulesDir) as $module) {
                    if ($module === '.' || $module === '..') continue;
                    $modulePath = $modulesDir . '/' . $module . '/Migrations';
                    if (is_dir($modulePath)) {
                        $paths[] = $modulePath;
                    }
                }
            }
        }

        return $paths;
    }
}
