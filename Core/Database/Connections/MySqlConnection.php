<?php

namespace Teguh02\Rijanphp\Core\Database\Connections;

use PDO;
use PDOException;
use Teguh02\Rijanphp\Core\Database\PdoConnection;

class MySqlConnection extends PdoConnection
{
    public function connect(array $config)
    {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Changed to ASSOC for consistency with current test expectations or simpler handling
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);
            return $this;
        } catch (PDOException $e) {
            throw new \Exception("Database Connection Error (MySQL): " . $e->getMessage());
        }
    }
}
