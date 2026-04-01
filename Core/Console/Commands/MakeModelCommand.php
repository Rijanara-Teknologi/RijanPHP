<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

class MakeModelCommand
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
            echo "\033[31mError:\033[0m Model name is required.\n";
            echo "Usage: php rijan make:model <ModelName> [--module=ModuleName] [--table=table_name]\n";
            exit(1);
        }

        $name = str_replace('Model', '', $name);
        $moduleName = $this->getOption($args, '--module');
        $table = $this->getOption($args, '--table') ?? strtolower($name . 's');

        if ($moduleName) {
            $this->makeModuleModel($name, $moduleName, $table);
        } else {
            $this->makeMasterModel($name, $table);
        }
    }

    protected function makeModuleModel(string $name, string $module, string $table): void
    {
        $path = $this->basePath . "Modules/{$module}/Models/{$name}Model.php";

        if (file_exists($path)) {
            echo "\033[31mError:\033[0m Model '{$name}Model' already exists in module '{$module}'.\n";
            exit(1);
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $namespace = "Teguh\\Rijanphp\\Modules\\{$module}\\Models";
        $content = $this->generateContent($name, $namespace, $table);
        file_put_contents($path, $content);

        echo "\033[32mCreated:\033[0m Modules/{$module}/Models/{$name}Model.php\n";
    }

    protected function makeMasterModel(string $name, string $table): void
    {
        $path = $this->basePath . "Master/Models/{$name}Model.php";

        if (file_exists($path)) {
            echo "\033[31mError:\033[0m Model '{$name}Model' already exists.\n";
            exit(1);
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $namespace = "Teguh\\Rijanphp\\Master\\Models";
        $content = $this->generateContent($name, $namespace, $table);
        file_put_contents($path, $content);

        echo "\033[32mCreated:\033[0m Master/Models/{$name}Model.php\n";
    }

    protected function generateContent(string $name, string $namespace, string $table): string
    {
        return <<<PHP
<?php
namespace {$namespace};

use Teguh02\Rijanphp\Core\Model\Model;

class {$name}Model extends Model
{
    protected \$table = '{$table}';
    protected \$primaryKey = 'id';
    protected \$returnType = 'object';
    protected \$allowedFields = [];
    protected \$useTimestamps = true;
    protected \$useSoftDeletes = false;
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
