<?php
namespace Teguh02\Rijanphp\Core\Router;

use Teguh02\Rijanphp\Core\Rijan;

class Router
{
    protected static $routes = [];
    protected static $groupStack = [];
    protected static $namedRoutes = [];

    /**
     * Clear all registered routes.
     */
    public static function clear()
    {
        self::$routes = [];
        self::$groupStack = [];
        self::$namedRoutes = [];
    }

    public static function get($uri, $action)
    {
        return self::add('GET', $uri, $action);
    }

    public static function post($uri, $action)
    {
        return self::add('POST', $uri, $action);
    }

    public static function put($uri, $action)
    {
        return self::add('PUT', $uri, $action);
    }

    public static function delete($uri, $action)
    {
        return self::add('DELETE', $uri, $action);
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
            $prefix .= '/' . ($group['prefix'] ?? '');
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
            'name' => null,
            'namespace' => \Teguh02\Rijanphp\Core\Modules\Register::$currentNamespace
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
            $isMatch = self::match($route['uri'], $uri);

            if ($route['method'] === $method && $isMatch) {
                return self::execute($route, $request);
            }
        }

        http_response_code(404);
        echo \Teguh02\Rijanphp\Core\View\View::render('core::errors.404');
        return;
    }

    protected static function match($routeUri, $requestUri)
    {
        $routeUri = rtrim($routeUri, '/');
        $requestUri = rtrim($requestUri, '/');

        if ($routeUri === '')
            $routeUri = '/';
        if ($requestUri === '')
            $requestUri = '/';

        // Convert {param} to regex group
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routeUri);
        $pattern = "#^" . $pattern . "$#";

        return preg_match($pattern, $requestUri);
    }

    protected static function execute($route, $request)
    {
        $action = $route['action'];
        $middlewareList = $route['middleware'];

        // Execute route-specific middleware
        foreach ($middlewareList as $middlewareClass) {
            if (class_exists($middlewareClass)) {
                $m = new $middlewareClass();
                if (method_exists($m, 'handle')) {
                    $m->handle();
                }
            }
        }

        // Extract parameters
        $params = [];
        $routeUri = rtrim($route['uri'], '/');
        if ($routeUri === '')
            $routeUri = '/';

        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routeUri);
        $pattern = "#^" . $pattern . "$#";

        if (preg_match($pattern, rtrim($request->uri(), '/'), $matches)) {
            array_shift($matches); // Remove full match
            $params = $matches;
        }

        if (is_array($action)) {
            $controllerClass = $action[0];
            $method = $action[1];

            \Teguh02\Rijanphp\Core\View\View::setCurrentNamespace($route['namespace'] ?? null);

            $controller = new $controllerClass($request);
            return call_user_func_array([$controller, $method], $params);
        }

        if (is_callable($action)) {
            \Teguh02\Rijanphp\Core\View\View::setCurrentNamespace($route['namespace'] ?? null);
            return call_user_func_array($action, array_merge([$request], $params));
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
