<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

class MakeControllerCommand
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
            echo "\033[31mError:\033[0m Controller name is required.\n";
            echo "Usage: php rijan make:controller <ControllerName> [--module=ModuleName]\n";
            exit(1);
        }

        $name = str_replace('Controller', '', $name);
        $moduleName = $this->getOption($args, '--module');

        if ($moduleName) {
            $this->makeModuleController($name, $moduleName);
        } else {
            $this->makeMasterController($name);
        }
    }

    protected function makeModuleController(string $name, string $module): void
    {
        $path = $this->basePath . "Modules/{$module}/Controllers/{$name}Controller.php";

        if (file_exists($path)) {
            echo "\033[31mError:\033[0m Controller '{$name}Controller' already exists in module '{$module}'.\n";
            exit(1);
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $namespace = "Teguh\\Rijanphp\\Modules\\{$module}\\Controllers";
        $content = $this->generateContent($name, $namespace);
        file_put_contents($path, $content);

        echo "\033[32mCreated:\033[0m Modules/{$module}/Controllers/{$name}Controller.php\n";
    }

    protected function makeMasterController(string $name): void
    {
        $path = $this->basePath . "Master/Controllers/{$name}Controller.php";

        if (file_exists($path)) {
            echo "\033[31mError:\033[0m Controller '{$name}Controller' already exists.\n";
            exit(1);
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $namespace = "Teguh\\Rijanphp\\Master\\Controllers";
        $content = $this->generateContent($name, $namespace);
        file_put_contents($path, $content);

        echo "\033[32mCreated:\033[0m Master/Controllers/{$name}Controller.php\n";
    }

    protected function generateContent(string $name, string $namespace): string
    {
        return <<<PHP
<?php
namespace {$namespace};

use Teguh02\Rijanphp\Core\Controller\Controller;

class {$name}Controller extends Controller
{
    public function index()
    {
        return view('{$name}.index');
    }

    public function show(\$id)
    {
        return view('{$name}.show', ['id' => \$id]);
    }

    public function store()
    {
        \$data = \$this->request->all();
        return response()->json(['status' => 'created', 'data' => \$data]);
    }

    public function update(\$id)
    {
        \$data = \$this->request->all();
        return response()->json(['status' => 'updated', 'id' => \$id, 'data' => \$data]);
    }

    public function destroy(\$id)
    {
        return response()->json(['status' => 'deleted', 'id' => \$id]);
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
