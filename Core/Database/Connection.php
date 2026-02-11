<?php

namespace Teguh02\Rijanphp\Core\Database;

interface Connection
{
    /**
     * Establish a database connection.
     *
     * @param array $config
     * @return $this
     */
    public function connect(array $config);

    /**
     * Execute a query.
     *
     * @param string $sql
     * @param array $bindings
     * @return \PDOStatement
     */
    public function query($sql, $bindings = []);

    /**
     * Fetch a single row.
     *
     * @param string $sql
     * @param array $bindings
     * @return mixed
     */
    public function fetch($sql, $bindings = []);

    /**
     * Fetch all rows.
     *
     * @param string $sql
     * @param array $bindings
     * @return array
     */
    public function fetchAll($sql, $bindings = []);

    /**
     * Get the last inserted ID.
     *
     * @return string
     */
    public function lastInsertId();

    /**
     * Begin a transaction.
     *
     * @return bool
     */
    public function beginTransaction();

    /**
     * Commit a transaction.
     *
     * @return bool
     */
    public function commit();

    /**
     * Rollback a transaction.
     *
     * @return bool
     */
    public function rollBack();
}
