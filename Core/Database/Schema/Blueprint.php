<?php

namespace Teguh02\Rijanphp\Core\Database\Schema;

class Blueprint
{
    protected $table;
    protected $columns = [];
    protected $primaryKey = null;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function id($column = 'id')
    {
        $driver = getenv('DB_CONNECTION') ?: 'sqlite';

        if ($driver === 'pgsql') {
            $this->columns[] = "{$column} SERIAL PRIMARY KEY";
            $this->primaryKey = $column;
            return $this;
        }

        $autoIncrement = ($driver === 'mysql') ? 'AUTO_INCREMENT' : 'AUTOINCREMENT';

        // MySQL uses INT, SQLite uses INTEGER for auto-increment primary keys
        $type = ($driver === 'mysql') ? 'INT' : 'INTEGER';

        $this->columns[] = "{$column} {$type} PRIMARY KEY {$autoIncrement}";
        $this->primaryKey = $column;
        return $this;
    }

    public function string($column, $length = 255)
    {
        $this->columns[] = "{$column} VARCHAR({$length})";
        return $this;
    }

    public function integer($column)
    {
        $this->columns[] = "{$column} INTEGER";
        return $this;
    }

    public function text($column)
    {
        $this->columns[] = "{$column} TEXT";
        return $this;
    }

    public function raw($definition)
    {
        $this->columns[] = $definition;
        return $this;
    }

    public function timestamps()
    {
        $this->columns[] = "created_at DATETIME DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at DATETIME DEFAULT CURRENT_TIMESTAMP";
        return $this;
    }

    public function toSql()
    {
        $columns = implode(', ', $this->columns);
        return "CREATE TABLE IF NOT EXISTS {$this->table} ({$columns})";
    }
}
