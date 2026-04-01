<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

use Teguh02\Rijanphp\Core\Router\Router;
use Teguh02\Rijanphp\Core\Modules\Executor;

class RouteListCommand
{
    protected $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function handle(array $args)
    {
        echo "\n\033[1mRegistered Routes\033[0m\n\n";

        try {
            $modulesConfig = config('modules');

            if (isset($modulesConfig['modules']) && is_array($modulesConfig['modules'])) {
                foreach ($modulesConfig['modules'] as $moduleClass) {
                    if (class_exists($moduleClass)) {
                        new $moduleClass();
                    }
                }
            }

            $routes = Router::getRoutes();

            if (empty($routes)) {
                echo "\033[33mNo routes registered.\033[0m\n";
                return;
            }

            $headers = ['Method', 'URI', 'Name', 'Middleware'];
            $rows = [];

            foreach ($routes as $method => $routeList) {
                foreach ($routeList as $route) {
                    $action = is_array($route['action'])
                        ? implode('@', $route['action'])
                        : 'Closure';

                    $rows[] = [
                        $method,
                        $route['path'],
                        $route['name'] ?? '-',
                        $route['middleware'] ? implode(', ', $route['middleware']) : '-',
                    ];
                }
            }

            $this->table($headers, $rows);
            echo "\n";
        } catch (\Exception $e) {
            echo "\033[31mError:\033[0m " . $e->getMessage() . "\n";
        }
    }

    protected function table(array $headers, array $rows)
    {
        $widths = array_map('strlen', $headers);

        foreach ($rows as $row) {
            foreach ($row as $i => $cell) {
                $widths[$i] = max($widths[$i] ?? 0, strlen($cell));
            }
        }

        $separator = $this->buildSeparator($widths);

        echo $separator . "\n";
        echo $this->buildRow($headers, $widths) . "\n";
        echo $separator . "\n";

        foreach ($rows as $row) {
            echo $this->buildRow($row, $widths) . "\n";
        }

        echo $separator . "\n";
    }

    protected function buildRow(array $cells, array $widths): string
    {
        $parts = [];
        foreach ($cells as $i => $cell) {
            $parts[] = ' ' . str_pad($cell, $widths[$i]) . ' ';
        }
        return '|' . implode('|', $parts) . '|';
    }

    protected function buildSeparator(array $widths): string
    {
        $parts = [];
        foreach ($widths as $width) {
            $parts[] = str_repeat('-', $width + 2);
        }
        return '+' . implode('+', $parts) . '+';
    }
}
