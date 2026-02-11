<?php

namespace Teguh02\Rijanphp\Core\Database;

use PDO;
use PDOException;

abstract class PdoConnection implements Connection
{
    protected $pdo;

    /**
     * Establish a database connection.
     */
    abstract public function connect(array $config);

    /**
     * Execute a query.
     */
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

    /**
     * Fetch a single row.
     */
    public function fetch($sql, $bindings = [])
    {
        return $this->query($sql, $bindings)->fetch();
    }

    /**
     * Fetch all rows.
     */
    public function fetchAll($sql, $bindings = [])
    {
        return $this->query($sql, $bindings)->fetchAll();
    }

    /**
     * Get the last inserted ID.
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Begin a transaction.
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction.
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback a transaction.
     */
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    /**
     * Get the PDO instance.
     */
    public function getPdo()
    {
        return $this->pdo;
    }
}
