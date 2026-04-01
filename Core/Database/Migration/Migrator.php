<?php

namespace Teguh02\Rijanphp\Core\Database\Migration;

use Teguh02\Rijanphp\Core\Database\Schema\Schema;
use Teguh02\Rijanphp\Core\Database\Schema\Blueprint;

class Migrator
{
    public function install()
    {
        Schema::create('migrations', function (Blueprint $table) {
            $table->id();
            $table->string('migration');
            $table->integer('batch');
        });
    }

    public function run($paths = [])
    {
        $this->ensureTableExists();

        $ran = $this->getRan();
        $files = $this->getMigrationFiles($paths);

        $batch = $this->getNextBatchNumber();

        foreach ($files as $file) {
            $migrationName = basename($file, '.php');

            if (!in_array($migrationName, $ran)) {
                require_once $file;

                // Assuming class name matches filename convention (e.g. 001_create_users -> CreateUsers)
                // For simplicity, let's assume the migration file returns an anonymous class or specific class
                // But typically we enforce psr-4 or require class name parsing.
                // Let's rely on declared class.
                $className = $this->resolveClassName($migrationName);
                if (class_exists($className)) {
                    $migration = new $className;
                    $migration->up();

                    $this->log($migrationName, $batch);
                    echo "Migrated: {$migrationName}\n";
                }
            }
        }
    }

    protected function ensureTableExists()
    {
        try {
            db()->table('migrations')->limit(1)->get();
        } catch (\Exception $e) {
            $this->install();
        }
    }

    protected function getRan()
    {
        return db()->table('migrations')->pluck('migration');
    }

    protected function getMigrationFiles($paths)
    {
        $files = [];
        foreach ($paths as $path) {
            foreach (glob($path . '/*.php') as $file) {
                $files[] = $file;
            }
        }
        sort($files);
        return $files;
    }

    protected function getNextBatchNumber()
    {
        return (int) db()->table('migrations')->max('batch') + 1;
    }

    protected function log($migration, $batch)
    {
        db()->table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch
        ]);
    }

    protected function resolveClassName($migrationName)
    {
        // Convert snake_case to PascalCase
        // Removing numeric prefix first: 001_create_users_table -> CreateUsersTable
        $name = preg_replace('/^\d+_/', '', $migrationName);
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    }

    public function rollback()
    {
        $this->ensureTableExists();

        $lastBatch = (int) db()->table('migrations')->max('batch');

        if ($lastBatch <= 0) {
            return [];
        }

        $migrations = db()->table('migrations')->where('batch', $lastBatch)->get();
        $rolledBack = [];

        foreach ($migrations as $migration) {
            $migrationName = $migration['migration'];
            $files = $this->getAllMigrationFiles();

            $filePath = null;
            foreach ($files as $file) {
                if (basename($file, '.php') === $migrationName) {
                    $filePath = $file;
                    break;
                }
            }

            if ($filePath) {
                require_once $filePath;
                $className = $this->resolveClassName($migrationName);
                if (class_exists($className)) {
                    $migrationObj = new $className;
                    $migrationObj->down();
                    $rolledBack[] = $migrationName;
                }
            }

            db()->table('migrations')->where('migration', $migrationName)->delete();
        }

        return $rolledBack;
    }

    public function status()
    {
        $this->ensureTableExists();

        $ran = $this->getRan();
        $allFiles = $this->getAllMigrationFiles();
        $status = [];

        foreach ($allFiles as $file) {
            $migrationName = basename($file, '.php');
            $isMigrated = in_array($migrationName, $ran);

            $batch = null;
            if ($isMigrated) {
                $row = db()->table('migrations')->where('migration', $migrationName)->first();
                $batch = $row['batch'] ?? null;
            }

            $status[] = [
                'name' => $migrationName,
                'migrated' => $isMigrated,
                'batch' => $batch,
            ];
        }

        return $status;
    }

    protected function getAllMigrationFiles()
    {
        $basePath = dirname(dirname(dirname(__DIR__))) . '/';
        $paths = [$basePath . 'Master/Migrations'];

        $modulesDir = $basePath . 'Modules';
        if (is_dir($modulesDir)) {
            foreach (scandir($modulesDir) as $module) {
                if ($module === '.' || $module === '..') continue;
                $modulePath = $modulesDir . '/' . $module . '/Migrations';
                if (is_dir($modulePath)) {
                    $paths[] = $modulePath;
                }
            }
        }

        return $this->getMigrationFiles($paths);
    }
}
