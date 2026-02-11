<?php
namespace Teguh02\Rijanphp\Core;

use Teguh02\Rijanphp\Core\Modules\Executor;

class Rijan
{
    public $base_path = null;

    public $config_path = null;

    public $request = [];
    public $cookie = [];
    public $session = [];

    public $server = [];

    public $env = [];

    public $db;
    public $log;

    final public const VERSION = '1.0.0';

    public static $instance;

    public function __construct()
    {
        self::$instance = $this;
    }

    protected function loadHelpers()
    {
        $helpers = [
            $this->base_path . 'Core/Helpers/*.php',
            $this->base_path . 'Master/Helpers/*.php',
        ];

        foreach ($helpers as $pattern) {
            foreach (glob($pattern) as $file) {
                require_once $file;
            }
        }
    }

    /**
     * Set or get the base path of the application.
     */
    public function base_path(?string $path = null)
    {
        if (is_null($this->base_path)) {
            if (is_null($path)) {
                return null;
            }
            $this->base_path = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
            $this->loadEnv();
            $this->loadHelpers();
            return $this;
        }

        $path = $path ? ltrim($path, '/\\') : '';
        return $this->base_path . $path;
    }

    /**
     * Load environment variables from .env file.
     */
    protected function loadEnv()
    {
        $file = $this->base_path . '.env';
        if (!file_exists($file)) {
            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            if (strpos(trim($line), '=') === false) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }

    /**
     * Set or get the configuration path of the application.
     */
    public function config(?string $path = null)
    {
        if (is_null($path)) {
            return $this->config_path;
        }

        $this->config_path = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * Set the request data.
     */
    public function request(array $data)
    {
        $this->request = $data;
        return $this;
    }

    /**
     * Set the server data.
     */
    public function server(array $data)
    {
        $this->server = $data;
        return $this;
    }

    /**
     * Set the environment data.
     */
    public function env(array $data)
    {
        $this->env = $data;
        return $this;
    }

    /**
     * Set the cookie data.
     */
    public function cookie(array $data)
    {
        $this->cookie = $data;
        return $this;
    }

    /**
     * Set the session data.
     */
    public function session(array $data)
    {
        $this->session = $data;
        return $this;
    }

    final public static function version(): string
    {
        return self::VERSION;
    }

    /**
     * Run the application.
     */
    public function run()
    {
        // Initialize Request
        \Teguh02\Rijanphp\Core\Http\Request::$instance = new \Teguh02\Rijanphp\Core\Http\Request();

        // Initialize Database Manager
        $this->db = \Teguh02\Rijanphp\Core\Database\DatabaseManager::getInstance();

        // Initialize Log Manager
        $this->log = \Teguh02\Rijanphp\Core\Log\LogManager::getInstance();

        // Run global middleware
        (new \Teguh02\Rijanphp\Core\Middleware\StartSession())->handle();
        (new \Teguh02\Rijanphp\Core\Middleware\EncryptCookies())->handle();
        (new \Teguh02\Rijanphp\Core\Middleware\LogRequests())->handle();
        (new \Teguh02\Rijanphp\Master\Middleware\VerifyCsrfToken())->handle();

        $response = Executor::Run();

        if ($response instanceof \Teguh02\Rijanphp\Core\Http\Response) {
            $response->send();
        } elseif (is_string($response) || is_numeric($response)) {
            echo $response;
        }
    }
}