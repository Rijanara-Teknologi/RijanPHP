<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

class MakeViewCommand
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
            echo "\033[31mError:\033[0m View name is required.\n";
            echo "Usage: php rijan make:view <view.name> [--module=ModuleName]\n";
            exit(1);
        }

        $moduleName = $this->getOption($args, '--module');

        if ($moduleName) {
            $this->makeModuleView($name, $moduleName);
        } else {
            $this->makeMasterView($name);
        }
    }

    protected function makeModuleView(string $name, string $module): void
    {
        $file = str_replace(['.', '/'], DIRECTORY_SEPARATOR, $name) . '.php';
        $path = $this->basePath . "Modules/{$module}/Views/{$file}";

        if (file_exists($path)) {
            echo "\033[31mError:\033[0m View '{$name}' already exists in module '{$module}'.\n";
            exit(1);
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $content = $this->generateContent($name);
        file_put_contents($path, $content);

        echo "\033[32mCreated:\033[0m Modules/{$module}/Views/{$file}\n";
    }

    protected function makeMasterView(string $name): void
    {
        $file = str_replace(['.', '/'], DIRECTORY_SEPARATOR, $name) . '.php';
        $path = $this->basePath . "Master/Views/{$file}";

        if (file_exists($path)) {
            echo "\033[31mError:\033[0m View '{$name}' already exists.\n";
            exit(1);
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $content = $this->generateContent($name);
        file_put_contents($path, $content);

        echo "\033[32mCreated:\033[0m Master/Views/{$file}\n";
    }

    protected function generateContent(string $name): string
    {
        return <<<PHP
<?php extends_layout('master::layouts/main'); ?>

<?php section('content'); ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">{$name}</h1>
    <p>View content goes here.</p>
</div>
<?php end_section(); ?>
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
