<?php

namespace Teguh02\Rijanphp\Core\Database;

class QueryBuilder
{
    protected $connection;
    protected $table;
    protected $select = '*';
    protected $wheres = [];
    protected $bindings = [];
    protected $orders = [];
    protected $limit;
    protected $offset;
    protected $joins = [];

    protected const ALLOWED_ORDER_DIRECTIONS = ['ASC', 'DESC'];
    protected const ALLOWED_JOIN_TYPES = ['INNER', 'LEFT', 'RIGHT', 'CROSS', 'FULL'];

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function table($table)
    {
        $this->table = $this->sanitizeIdentifier($table);
        return $this;
    }

    public function select($columns = ['*'])
    {
        if (is_array($columns)) {
            $this->select = implode(', ', array_map([$this, 'sanitizeIdentifier'], $columns));
        } else {
            $this->select = $columns === '*' ? '*' : $this->sanitizeIdentifier($columns);
        }
        return $this;
    }

    public function where($column, $operator = null, $value = null)
    {
        if ($value === null && func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        if ($value === null) {
            $this->wheres[] = [
                'type' => 'AND',
                'sql' => "{$this->sanitizeIdentifier($column)} IS NULL",
                'binding' => null,
            ];
        } else {
            $this->wheres[] = [
                'type' => 'AND',
                'sql' => "{$this->sanitizeIdentifier($column)} {$this->sanitizeOperator($operator)} ?",
                'binding' => $value,
            ];
        }
        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        if ($value === null && func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        if ($value === null) {
            $this->wheres[] = [
                'type' => 'OR',
                'sql' => "{$this->sanitizeIdentifier($column)} IS NULL",
                'binding' => null,
            ];
        } else {
            $this->wheres[] = [
                'type' => 'OR',
                'sql' => "{$this->sanitizeIdentifier($column)} {$this->sanitizeOperator($operator)} ?",
                'binding' => $value,
            ];
        }
        return $this;
    }

    public function whereIn($column, array $values)
    {
        if (empty($values)) {
            $this->wheres[] = [
                'type' => 'AND',
                'sql' => '1 = 0',
                'binding' => null,
            ];
            return $this;
        }

        $column = $this->sanitizeIdentifier($column);
        $placeholders = implode(', ', array_fill(0, count($values), '?'));

        $this->wheres[] = [
            'type' => 'AND',
            'sql' => "{$column} IN ({$placeholders})",
            'binding' => $values,
        ];
        return $this;
    }

    public function whereNull($column)
    {
        $this->wheres[] = [
            'type' => 'AND',
            'sql' => "{$this->sanitizeIdentifier($column)} IS NULL",
            'binding' => null,
        ];
        return $this;
    }

    public function whereNotNull($column)
    {
        $this->wheres[] = [
            'type' => 'AND',
            'sql' => "{$this->sanitizeIdentifier($column)} IS NOT NULL",
            'binding' => null,
        ];
        return $this;
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $direction = strtoupper(trim($direction));
        if (!in_array($direction, self::ALLOWED_ORDER_DIRECTIONS, true)) {
            throw new \InvalidArgumentException("Order direction must be ASC or DESC, got: {$direction}");
        }

        $this->orders[] = "{$this->sanitizeIdentifier($column)} {$direction}";
        return $this;
    }

    public function orderByRaw($rawSql)
    {
        $this->orders[] = $rawSql;
        return $this;
    }

    public function latest($column = 'created_at')
    {
        return $this->orderBy($column, 'DESC');
    }

    public function oldest($column = 'created_at')
    {
        return $this->orderBy($column, 'ASC');
    }

    public function limit($limit, $offset = null)
    {
        $this->limit = (int) $limit;
        $this->offset = $offset !== null ? (int) $offset : null;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = (int) $offset;
        return $this;
    }

    public function get()
    {
        $sql = $this->compileSelect();
        return $this->connection->fetchAll($sql, $this->getBindings());
    }

    public function first()
    {
        $this->limit(1);
        $sql = $this->compileSelect();
        $result = $this->connection->fetch($sql, $this->getBindings());
        return $result ?: null;
    }

    public function value($column)
    {
        $result = $this->select([$column])->first();
        return $result[$column] ?? null;
    }

    public function exists()
    {
        $original = ['select' => $this->select, 'limit' => $this->limit, 'offset' => $this->offset];
        $this->select = '1';
        $this->limit = 1;
        $this->offset = null;

        $sql = $this->compileSelect();
        $result = $this->connection->fetch($sql, $this->getBindings());

        $this->select = $original['select'];
        $this->limit = $original['limit'];
        $this->offset = $original['offset'];

        return $result !== null && $result !== false;
    }

    public function doesntExist()
    {
        return !$this->exists();
    }

    public function count($column = '*')
    {
        return (int) $this->aggregate('COUNT', $column);
    }

    public function sum($column)
    {
        return $this->aggregate('SUM', $column);
    }

    public function avg($column)
    {
        return $this->aggregate('AVG', $column);
    }

    public function min($column)
    {
        return $this->aggregate('MIN', $column);
    }

    public function max($column)
    {
        return $this->aggregate('MAX', $column);
    }

    protected function aggregate($function, $column)
    {
        $originalSelect = $this->select;
        $this->select = "{$function}({$this->sanitizeIdentifier($column)}) as aggregate";

        $result = $this->first();

        $this->select = $originalSelect;

        return $result['aggregate'] ?? null;
    }

    public function insert(array $data)
    {
        if (empty($data)) {
            return false;
        }

        if (isset($data[0]) && is_array($data[0])) {
            $columns = implode(', ', array_map([$this, 'sanitizeIdentifier'], array_keys($data[0])));
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

        $columns = implode(', ', array_map([$this, 'sanitizeIdentifier'], array_keys($data)));
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
            $set[] = "{$this->sanitizeIdentifier($column)} = ?";
            $bindings[] = $value;
        }

        $setClause = implode(', ', $set);
        $bindings = array_merge($bindings, $this->getWhereBindings());
        $whereClause = $this->compileWhere();

        $sql = "UPDATE {$this->table} SET {$setClause} {$whereClause}";
        $stmt = $this->connection->query($sql, $bindings);
        return $stmt->rowCount();
    }

    public function delete()
    {
        $whereClause = $this->compileWhere();
        $sql = "DELETE FROM {$this->table} {$whereClause}";
        $stmt = $this->connection->query($sql, $this->getWhereBindings());
        return $stmt->rowCount();
    }

    public function join($table, $first, $operator, $second, $type = 'INNER')
    {
        $type = strtoupper(trim($type));
        if (!in_array($type, self::ALLOWED_JOIN_TYPES, true)) {
            throw new \InvalidArgumentException("Invalid join type: {$type}");
        }

        $this->joins[] = "{$type} JOIN {$this->sanitizeIdentifier($table)} ON {$this->sanitizeIdentifier($first)} {$this->sanitizeOperator($operator)} {$this->sanitizeIdentifier($second)}";
        return $this;
    }

    public function leftJoin($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function rightJoin($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    public function chunk($count, callable $callback)
    {
        $page = 1;
        do {
            $results = $this->limit($count, ($page - 1) * $count)->get();
            $countResults = count($results);

            if ($countResults === 0) {
                break;
            }

            if ($callback($results, $page) === false) {
                return false;
            }

            unset($results);
            $page++;
        } while ($countResults >= $count);

        return true;
    }

    protected function compileSelect()
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        $sql .= $this->compileWhere();

        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orders);
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
        if (empty($this->wheres)) {
            return '';
        }

        $clauses = [];
        foreach ($this->wheres as $i => $where) {
            $prefix = ($i === 0) ? 'WHERE' : $where['type'];
            $clauses[] = "{$prefix} {$where['sql']}";
        }

        return ' ' . implode(' ', $clauses);
    }

    protected function getBindings(): array
    {
        $bindings = [];
        foreach ($this->wheres as $where) {
            if (is_array($where['binding'])) {
                $bindings = array_merge($bindings, $where['binding']);
            } elseif ($where['binding'] !== null) {
                $bindings[] = $where['binding'];
            }
        }
        return $bindings;
    }

    protected function getWhereBindings(): array
    {
        return $this->getBindings();
    }

    public function query($sql, $bindings = [])
    {
        return $this->connection->query($sql, $bindings);
    }

    public function fetch($sql, $bindings = [])
    {
        return $this->connection->fetch($sql, $bindings);
    }

    public function fetchAll($sql, $bindings = [])
    {
        return $this->connection->fetchAll($sql, $bindings);
    }

    public function pluck($column)
    {
        $results = $this->select([$column])->get();
        return array_column($results, $column);
    }

    protected function sanitizeIdentifier($identifier): string
    {
        $identifier = trim($identifier);

        if ($identifier === '*') {
            return '*';
        }

        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
            return $identifier;
        }

        if (strpos($identifier, '.') !== false) {
            $parts = explode('.', $identifier);
            return implode('.', array_map(function($part) {
                return $this->sanitizeIdentifier(trim($part));
            }, $parts));
        }

        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*\s+(AS|as|As)\s+[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
            return $identifier;
        }

        throw new \InvalidArgumentException("Invalid identifier: {$identifier}");
    }

    protected function sanitizeOperator($operator): string
    {
        $operator = strtoupper(trim($operator));
        $allowed = ['=', '!=', '<>', '<', '>', '<=', '>=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'IS', 'IS NOT', 'BETWEEN', 'NOT BETWEEN'];

        if (!in_array($operator, $allowed, true)) {
            throw new \InvalidArgumentException("Invalid operator: {$operator}");
        }

        return $operator;
    }

    public function reset()
    {
        $this->select = '*';
        $this->wheres = [];
        $this->bindings = [];
        $this->orders = [];
        $this->limit = null;
        $this->offset = null;
        $this->joins = [];
        return $this;
    }
}
