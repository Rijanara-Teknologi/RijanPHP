<?php

namespace Teguh02\Rijanphp\Core\Database\Schema;

class Blueprint
{
    protected $table;
    protected $columns = [];
    protected $primaryKey = null;

    /** @var int|null Index of the last added column for fluent modifier chaining */
    protected $currentColumn = null;

    public function __construct($table)
    {
        $this->table = $table;
    }

    // -------------------------------------------------------------------------
    // Column type methods
    // -------------------------------------------------------------------------

    public function id($column = 'id')
    {
        $driver = getenv('DB_CONNECTION') ?: 'sqlite';

        if ($driver === 'pgsql') {
            $this->columns[] = "{$column} SERIAL PRIMARY KEY";
            $this->primaryKey = $column;
            $this->currentColumn = count($this->columns) - 1;
            return $this;
        }

        $autoIncrement = ($driver === 'mysql') ? 'AUTO_INCREMENT' : 'AUTOINCREMENT';

        // MySQL uses INT, SQLite uses INTEGER for auto-increment primary keys
        $type = ($driver === 'mysql') ? 'INT' : 'INTEGER';

        $this->columns[] = "{$column} {$type} PRIMARY KEY {$autoIncrement}";
        $this->primaryKey = $column;
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    public function string($column, $length = 255)
    {
        $this->columns[] = "{$column} VARCHAR({$length})";
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    public function integer($column)
    {
        $this->columns[] = "{$column} INTEGER";
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    public function bigInteger($column)
    {
        $driver = getenv('DB_CONNECTION') ?: 'sqlite';
        $type = ($driver === 'pgsql') ? 'BIGINT' : 'BIGINT';
        $this->columns[] = "{$column} {$type}";
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    public function unsignedBigInteger($column)
    {
        $driver = getenv('DB_CONNECTION') ?: 'sqlite';
        if ($driver === 'mysql') {
            $this->columns[] = "{$column} BIGINT UNSIGNED";
        } else {
            $this->columns[] = "{$column} BIGINT";
        }
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    public function boolean($column)
    {
        $driver = getenv('DB_CONNECTION') ?: 'sqlite';
        $type = ($driver === 'pgsql') ? 'BOOLEAN' : 'TINYINT(1)';
        $this->columns[] = "{$column} {$type}";
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    public function decimal($column, $precision = 8, $scale = 2)
    {
        $this->columns[] = "{$column} DECIMAL({$precision},{$scale})";
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    public function float($column, $precision = 8, $scale = 2)
    {
        $driver = getenv('DB_CONNECTION') ?: 'sqlite';
        if ($driver === 'pgsql') {
            $this->columns[] = "{$column} REAL";
        } elseif ($driver === 'mysql') {
            $this->columns[] = "{$column} FLOAT({$precision},{$scale})";
        } else {
            $this->columns[] = "{$column} REAL";
        }
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    public function text($column)
    {
        $this->columns[] = "{$column} TEXT";
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    public function timestamp($column)
    {
        $driver = getenv('DB_CONNECTION') ?: 'sqlite';
        $type = ($driver === 'pgsql') ? 'TIMESTAMP' : 'DATETIME';
        $this->columns[] = "{$column} {$type}";
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    public function date($column)
    {
        $this->columns[] = "{$column} DATE";
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    public function json($column)
    {
        $driver = getenv('DB_CONNECTION') ?: 'sqlite';
        $type = ($driver === 'pgsql' || $driver === 'mysql') ? 'JSON' : 'TEXT';
        $this->columns[] = "{$column} {$type}";
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    public function raw($definition)
    {
        $this->columns[] = $definition;
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    public function timestamps()
    {
        $driver = getenv('DB_CONNECTION') ?: 'sqlite';
        $type = ($driver === 'pgsql') ? 'TIMESTAMP' : 'DATETIME';

        $this->columns[] = "created_at {$type} DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at {$type} DEFAULT CURRENT_TIMESTAMP";
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Column modifier methods (fluent — operate on the last added column)
    // -------------------------------------------------------------------------

    public function nullable()
    {
        if ($this->currentColumn !== null) {
            $this->columns[$this->currentColumn] .= ' NULL';
        }
        return $this;
    }

    public function unique()
    {
        if ($this->currentColumn !== null) {
            $this->columns[$this->currentColumn] .= ' UNIQUE';
        }
        return $this;
    }

    public function default($value)
    {
        if ($this->currentColumn !== null) {
            if (is_string($value)) {
                $escaped = str_replace("'", "''", $value);
                $this->columns[$this->currentColumn] .= " DEFAULT '{$escaped}'";
            } elseif (is_bool($value)) {
                $this->columns[$this->currentColumn] .= ' DEFAULT ' . ($value ? '1' : '0');
            } elseif (is_null($value)) {
                $this->columns[$this->currentColumn] .= ' DEFAULT NULL';
            } else {
                $this->columns[$this->currentColumn] .= " DEFAULT {$value}";
            }
        }
        return $this;
    }

    // -------------------------------------------------------------------------
    // Special column helpers
    // -------------------------------------------------------------------------

    public function softDeletes($column = 'deleted_at')
    {
        $driver = getenv('DB_CONNECTION') ?: 'sqlite';
        $type = ($driver === 'pgsql') ? 'TIMESTAMP' : 'DATETIME';
        $this->columns[] = "{$column} {$type} NULL DEFAULT NULL";
        $this->currentColumn = count($this->columns) - 1;
        return $this;
    }

    public function foreign($column)
    {
        return new BlueprintForeignKey($this, $column);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getColumns()
    {
        return $this->columns;
    }

    public function toSql()
    {
        $columns = implode(', ', $this->columns);
        return "CREATE TABLE IF NOT EXISTS {$this->table} ({$columns})";
    }
}

class BlueprintForeignKey
{
    protected $blueprint;
    protected $column;
    protected $referencesColumn;
    protected $referencesTable;
    protected $onDeleteAction = 'RESTRICT';
    protected $onUpdateAction = 'RESTRICT';

    public function __construct(Blueprint $blueprint, $column)
    {
        $this->blueprint = $blueprint;
        $this->column = $column;
    }

    public function references($column)
    {
        $this->referencesColumn = $column;
        return $this;
    }

    public function on($table)
    {
        $this->referencesTable = $table;
        $this->blueprint->raw(
            "FOREIGN KEY ({$this->column}) REFERENCES {$this->referencesTable}({$this->referencesColumn})" .
            " ON DELETE {$this->onDeleteAction} ON UPDATE {$this->onUpdateAction}"
        );
        return $this->blueprint;
    }

    public function onDelete($action)
    {
        $this->onDeleteAction = strtoupper($action);
        return $this;
    }

    public function onUpdate($action)
    {
        $this->onUpdateAction = strtoupper($action);
        return $this;
    }
}
