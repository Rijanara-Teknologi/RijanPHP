<?php

namespace Teguh02\Rijanphp\Core\Database\Connections;

use PDO;
use PDOException;
use Teguh02\Rijanphp\Core\Database\PdoConnection;

class PgSqlConnection extends PdoConnection
{
    public function connect(array $config)
    {
        $port = $config['port'] ?? 5432;
        $dsn = "pgsql:host={$config['host']};port={$port};dbname={$config['database']}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);
            return $this;
        } catch (PDOException $e) {
            throw new \Exception("Database Connection Error (PostgreSQL): " . $e->getMessage());
        }
    }
}
