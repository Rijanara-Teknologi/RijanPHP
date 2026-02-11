<?php

namespace Teguh02\Rijanphp\Core\Log;

use Teguh02\Rijanphp\Core\Log\Handlers\FileHandler;

class LogManager
{
    protected static $instance;
    protected $channels = [];
    protected $config;

    public function __construct()
    {
        $this->config = config('logging');
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function channel($name = null)
    {
        $name = $name ?: ($this->config['default'] ?? 'single');

        if (!isset($this->channels[$name])) {
            $this->channels[$name] = $this->createChannel($name);
        }

        return $this->channels[$name];
    }

    protected function createChannel($name)
    {
        $config = $this->config['channels'][$name] ?? null;

        if (!$config) {
            throw new \Exception("Log channel [{$name}] not configured.");
        }

        switch ($config['driver']) {
            case 'single':
                return new Logger(new FileHandler($config['path']));
            // Add other drivers like 'daily', 'syslog', 'slack', 'stack' here
            default:
                throw new \Exception("Log driver [{$config['driver']}] not supported.");
        }
    }

    public function __call($method, $parameters)
    {
        return $this->channel()->$method(...$parameters);
    }
}
