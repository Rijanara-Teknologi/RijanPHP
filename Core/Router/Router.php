<?php
namespace Teguh02\Rijanphp\Core\Router;

use Teguh02\Rijanphp\Core\Rijan;

class Router
{
    protected static $routes = [];
    protected static $groupStack = [];
    protected static $namedRoutes = [];

    public static function get($uri, $action)
    {
        return self::add('GET', $uri, $action);
    }

    public static function post($uri, $action)
    {
        return self::add('POST', $uri, $action);
    }

    /**
     * Create a route group.
     */
    public static function group(array $attributes, $callback)
    {
        self::$groupStack[] = $attributes;

        if (is_callable($callback)) {
            $callback();
        }

        array_pop(self::$groupStack);
    }

    protected static function add($method, $uri, $action)
    {
        $prefix = '';
        $middleware = [];

        foreach (self::$groupStack as $group) {
            $prefix .= ($group['prefix'] ?? '');
            if (isset($group['middleware'])) {
                $middleware = array_merge($middleware, (array) $group['middleware']);
            }
        }

        $uri = '/' . trim($prefix . '/' . trim($uri, '/'), '/');
        if ($uri === '')
            $uri = '/';

        foreach (self::$routes as $route) {
            if ($route['method'] === $method && $route['uri'] === $uri) {
                throw new \Exception("Route [$method $uri] already defined.");
            }
        }

        self::$routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action,
            'middleware' => $middleware,
            'name' => null
        ];

        return new class {
            public function name($name)
            {
                \Teguh02\Rijanphp\Core\Router\Router::setName($name);
                return $this;
            }

            public function middleware($middleware)
            {
                \Teguh02\Rijanphp\Core\Router\Router::setMiddleware($middleware);
                return $this;
            }
        };
    }

    public static function setName($name)
    {
        $lastIndex = array_key_last(self::$routes);
        if ($lastIndex !== null) {
            self::$routes[$lastIndex]['name'] = $name;
            self::$namedRoutes[$name] = self::$routes[$lastIndex]['uri'];
        }
    }

    public static function setMiddleware($middleware)
    {
        $lastIndex = array_key_last(self::$routes);
        if ($lastIndex !== null) {
            self::$routes[$lastIndex]['middleware'] = array_merge(
                self::$routes[$lastIndex]['middleware'],
                (array) $middleware
            );
        }
    }

    public static function route($name, $params = [])
    {
        if (!isset(self::$namedRoutes[$name])) {
            throw new \Exception("Route [{$name}] not found.");
        }

        $uri = self::$namedRoutes[$name];

        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
        }

        return $uri;
    }

    public static function dispatch()
    {
        $request = \Teguh02\Rijanphp\Core\Http\Request::$instance ?? new \Teguh02\Rijanphp\Core\Http\Request();
        $uri = $request->uri();
        $method = $request->method();

        foreach (self::$routes as $route) {
            if ($route['method'] === $method && self::match($route['uri'], $uri)) {
                return self::execute($route, $request);
            }
        }

        echo "404 Not Found - Modular Router";
    }

    protected static function match($routeUri, $requestUri)
    {
        $routeUri = rtrim($routeUri, '/');
        $requestUri = rtrim($requestUri, '/');

        if ($routeUri === '')
            $routeUri = '/';
        if ($requestUri === '')
            $requestUri = '/';

        return $routeUri === $requestUri;
    }

    protected static function execute($route, $request)
    {
        $action = $route['action'];
        $middleware = $route['middleware'];

        // Simple middleware execution (Wait for Middleware class/logic)
        // For now, let's just run the action with Request injection.

        if (is_array($action)) {
            $controllerClass = $action[0];
            $method = $action[1];

            $controller = new $controllerClass($request);
            return call_user_func([$controller, $method]);
        }

        if (is_callable($action)) {
            return call_user_func($action, $request);
        }
    }

    public static function getRoutes()
    {
        $formatted = [];
        foreach (self::$routes as $route) {
            $formatted[$route['method']][] = [
                'path' => $route['uri'],
                'action' => $route['action'],
                'middleware' => $route['middleware'],
                'name' => $route['name']
            ];
        }
        return $formatted;
    }
}
