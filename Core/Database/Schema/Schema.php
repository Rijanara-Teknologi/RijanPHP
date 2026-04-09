<?php

namespace Teguh02\Rijanphp\Core\Database\Schema;

use Closure;

class Schema
{
    public static function table($table, Closure $callback)
    {
        $blueprint = new Blueprint($table);
        $blueprint->setModifying(true);
        $callback($blueprint);

        $statements = $blueprint->toAlterSql();
        foreach ($statements as $sql) {
            db()->query($sql);
        }
    }

    public static function create($table, Closure $callback)
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $sql = $blueprint->toSql();
        db()->query($sql);
    }

    public static function drop($table)
    {
        $sql = "DROP TABLE {$table}";
        db()->query($sql);
    }

    public static function dropIfExists($table)
    {
        $sql = "DROP TABLE IF EXISTS {$table}";
        db()->query($sql);
    }

    public static function hasTable($table)
    {
        try {
            $result = db()->query("SELECT 1 FROM {$table} LIMIT 1");
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
