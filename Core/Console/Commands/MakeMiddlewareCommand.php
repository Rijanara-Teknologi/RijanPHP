<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

class MakeMiddlewareCommand
{
    protected $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function handle(array $args)
    {
        $name = $args[0] ?? null;

        if (!$name) {
            echo "\033[31mError:\033[0m Middleware name is required.\n";
            echo "Usage: php rijan make:middleware <MiddlewareName> [--module=ModuleName]\n";
            exit(1);
        }

        $moduleName = $this->getOption($args, '--module');

        if ($moduleName) {
            $this->makeModuleMiddleware($name, $moduleName);
        } else {
            $this->makeMasterMiddleware($name);
        }
    }

    protected function makeModuleMiddleware(string $name, string $module): void
    {
        $path = $this->basePath . "Modules/{$module}/Middleware/{$name}.php";

        if (file_exists($path)) {
            echo "\033[31mError:\033[0m Middleware '{$name}' already exists in module '{$module}'.\n";
            exit(1);
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $namespace = "Teguh\\Rijanphp\\Modules\\{$module}\\Middleware";
        $content = $this->generateContent($name, $namespace);
        file_put_contents($path, $content);

        echo "\033[32mCreated:\033[0m Modules/{$module}/Middleware/{$name}.php\n";
    }

    protected function makeMasterMiddleware(string $name): void
    {
        $path = $this->basePath . "Master/Middleware/{$name}.php";

        if (file_exists($path)) {
            echo "\033[31mError:\033[0m Middleware '{$name}' already exists.\n";
            exit(1);
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $namespace = "Teguh\\Rijanphp\\Master\\Middleware";
        $content = $this->generateContent($name, $namespace);
        file_put_contents($path, $content);

        echo "\033[32mCreated:\033[0m Master/Middleware/{$name}.php\n";
    }

    protected function generateContent(string $name, string $namespace): string
    {
        return <<<PHP
<?php
namespace {$namespace};

class {$name}
{
    public function handle()
    {
        
    }
}
PHP;
    }

    protected function getOption(array $args, string $option)
    {
        foreach ($args as $arg) {
            if (strpos($arg, $option . '=') === 0) {
                return substr($arg, strlen($option . '='));
            }
        }
        return null;
    }
}
