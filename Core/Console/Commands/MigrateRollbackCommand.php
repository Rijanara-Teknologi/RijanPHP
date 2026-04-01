<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

use Teguh02\Rijanphp\Core\Database\Migration\Migrator;

class MigrateRollbackCommand
{
    protected $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function handle(array $args)
    {
        echo "\n\033[1mRolling Back Migrations...\033[0m\n\n";

        $migrator = new Migrator();

        try {
            $rolledBack = $migrator->rollback();

            if (empty($rolledBack)) {
                echo "\033[33mNothing to rollback.\033[0m\n";
            } else {
                foreach ($rolledBack as $migration) {
                    echo "\033[33mRolled back:\033[0m {$migration}\n";
                }
                echo "\n\033[33mSuccessfully rolled back " . count($rolledBack) . " migration(s)!\033[0m\n";
            }
        } catch (\Exception $e) {
            echo "\033[31mRollback failed:\033[0m " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}
