<?php

namespace Teguh02\Rijanphp\Core\Database;

use PDO;
use PDOException;

abstract class PdoConnection implements Connection
{
    protected $pdo;
    protected $driver;

    abstract public function connect(array $config);

    public function query($sql, $bindings = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($bindings);
            return $stmt;
        } catch (PDOException $e) {
            throw new \Exception("Database Query Error: " . $e->getMessage() . " | SQL: " . $sql);
        }
    }

    public function fetch($sql, $bindings = [])
    {
        return $this->query($sql, $bindings)->fetch();
    }

    public function fetchAll($sql, $bindings = [])
    {
        return $this->query($sql, $bindings)->fetchAll();
    }

    public function lastInsertId($sequence = null)
    {
        if ($this->driver === 'pgsql' && $sequence !== null) {
            return $this->pdo->lastInsertId($sequence);
        }

        return $this->pdo->lastInsertId();
    }

    public function getDriver(): string
    {
        return $this->driver ?? 'unknown';
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}
