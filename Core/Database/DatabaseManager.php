<?php

namespace Teguh02\Rijanphp\Core\Database;

use Teguh02\Rijanphp\Core\Database\Connections\MySqlConnection;

class DatabaseManager
{
    protected static $instance;
    protected $connections = [];
    protected $config;

    public function __construct()
    {
        $this->config = config('database');
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function connection($name = null)
    {
        $name = $name ?: $this->config['default'];

        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->makeConnection($name);
        }

        return $this->connections[$name];
    }

    protected function makeConnection($name)
    {
        $config = $this->config['connections'][$name];

        switch ($config['driver']) {
            case 'mysql':
                return (new \Teguh02\Rijanphp\Core\Database\Connections\MySqlConnection())->connect($config);
            case 'pgsql':
                return (new \Teguh02\Rijanphp\Core\Database\Connections\PgSqlConnection())->connect($config);
            case 'sqlite':
                return (new \Teguh02\Rijanphp\Core\Database\Connections\SqliteConnection())->connect($config);
            default:
                throw new \Exception("Database driver [{$config['driver']}] not supported.");
        }
    }

    public function close($name = null)
    {
        if (is_null($name)) {
            $this->connections = [];
        } else {
            unset($this->connections[$name]);
        }
    }

    public function __call($method, $parameters)
    {
        return $this->connection()->$method(...$parameters);
    }
}
