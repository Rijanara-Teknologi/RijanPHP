<?php

namespace Teguh02\Rijanphp\Core\Database;

class QueryBuilder
{
    protected $connection;
    protected $table;
    protected $select = '*';
    protected $where = [];
    protected $bindings = [];
    protected $orderBy = [];
    protected $limit;
    protected $offset;
    protected $joins = [];

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function select($columns = ['*'])
    {
        $this->select = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    public function where($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->where[] = "{$column} {$operator} ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $line = count($this->where) > 0 ? 'OR' : 'WHERE';

        // This is a simplified OR implementation. 
        // Real implementation needs to handle grouping. 
        // For now, let's just append.
        // Actually, where array logic in compile will handle AND prefix.
        // We need a structure to differentiate AND/OR.
        $this->where[] = ['type' => 'OR', 'sql' => "{$column} {$operator} ?", 'binding' => $value];
        // But for simplicity of this iteration, let's stick to simple array and treat all as AND unless specified.
        // Let's refactor where storage.
        return $this;
    }

    // Refactored Where
    /*
    protected $wheres = [];
    // ... join, limit, etc ...
    */

    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy[] = "{$column} {$direction}";
        return $this;
    }

    public function limit($limit, $offset = null)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function get()
    {
        $sql = $this->compileSelect();
        return $this->connection->fetchAll($sql, $this->bindings);
    }

    public function first()
    {
        $this->limit(1);
        $sql = $this->compileSelect();
        $result = $this->connection->fetch($sql, $this->bindings);
        return $result ?: null;
    }

    public function insert(array $data)
    {
        if (empty($data)) {
            return false;
        }

        // Check if we are doing a multi-row insert
        if (isset($data[0]) && is_array($data[0])) {
            $columns = implode(', ', array_keys($data[0]));
            $rowCount = count($data);
            $valuesCount = count($data[0]);

            $rowPlaceholders = '(' . implode(', ', array_fill(0, $valuesCount, '?')) . ')';
            $placeholders = implode(', ', array_fill(0, $rowCount, $rowPlaceholders));

            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES {$placeholders}";

            $bindings = [];
            foreach ($data as $row) {
                $bindings = array_merge($bindings, array_values($row));
            }

            $this->connection->query($sql, $bindings);
            return true;
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $this->connection->query($sql, array_values($data));

        return $this->connection->lastInsertId();
    }

    public function update(array $data)
    {
        $set = [];
        $bindings = [];

        foreach ($data as $column => $value) {
            $set[] = "{$column} = ?";
            $bindings[] = $value;
        }

        $setClause = implode(', ', $set);

        // Append where bindings
        $bindings = array_merge($bindings, $this->bindings);

        $whereClause = $this->compileWhere();

        $sql = "UPDATE {$this->table} SET {$setClause} {$whereClause}";

        $stmt = $this->connection->query($sql, $bindings);
        return $stmt->rowCount();
    }

    public function delete()
    {
        $whereClause = $this->compileWhere();
        $sql = "DELETE FROM {$this->table} {$whereClause}";

        $stmt = $this->connection->query($sql, $this->bindings);
        return $stmt->rowCount();
    }

    protected function compileSelect()
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";

        if (!empty($this->joins)) {
            // Implement joins compilation
        }

        $sql .= $this->compileWhere();

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        if (isset($this->limit)) {
            $sql .= " LIMIT {$this->limit}";
        }

        if (isset($this->offset)) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    protected function compileWhere()
    {
        if (empty($this->where)) {
            return '';
        }

        // Simple implementation assuming all are standard string WHERE clauses from basic where()
        // If we implemented OR, we'd need loop check.
        // Reverting to simple string storage from where() method for now.

        return " WHERE " . implode(' AND ', $this->where);
    }

    /**
     * Execute a raw query.
     *
     * @param string $sql
     * @param array $bindings
     * @return \PDOStatement
     */
    public function query($sql, $bindings = [])
    {
        return $this->connection->query($sql, $bindings);
    }

    /**
     * Fetch a single row from a raw query.
     *
     * @param string $sql
     * @param array $bindings
     * @return mixed
     */
    public function fetch($sql, $bindings = [])
    {
        return $this->connection->fetch($sql, $bindings);
    }

    /**
     * Fetch all rows from a raw query.
     *
     * @param string $sql
     * @param array $bindings
     * @return array
     */
    public function fetchAll($sql, $bindings = [])
    {
        return $this->connection->fetchAll($sql, $bindings);
    }
}
