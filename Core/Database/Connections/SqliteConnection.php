<?php

namespace Teguh02\Rijanphp\Core\Database\Connections;

use PDO;
use PDOException;
use Teguh02\Rijanphp\Core\Database\PdoConnection;

class SqliteConnection extends PdoConnection
{
    public function connect(array $config)
    {
        $dsn = "sqlite:{$config['database']}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, null, null, $options);
            return $this;
        } catch (PDOException $e) {
            throw new \Exception("Database Connection Error (SQLite): " . $e->getMessage());
        }
    }
}
