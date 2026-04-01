<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

class MakeModuleCommand
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
            echo "\033[31mError:\033[0m Module name is required.\n";
            echo "Usage: php rijan make:module <ModuleName>\n";
            exit(1);
        }

        $moduleDir = $this->basePath . 'Modules/' . $name;

        if (is_dir($moduleDir)) {
            echo "\033[31mError:\033[0m Module '{$name}' already exists.\n";
            exit(1);
        }

        $directories = [
            'Controllers',
            'Models',
            'Views',
            'Routes',
        ];

        foreach ($directories as $dir) {
            $path = $moduleDir . '/' . $dir;
            mkdir($path, 0755, true);
            echo "\033[32mCreated:\033[0m Modules/{$name}/{$dir}/\n";
        }

        $moduleClass = $this->generateModuleClass($name);
        file_put_contents($moduleDir . '/' . $name . '.php', $moduleClass);
        echo "\033[32mCreated:\033[0m Modules/{$name}/{$name}.php\n";

        $routes = $this->generateRoutes($name);
        file_put_contents($moduleDir . '/Routes/web.php', $routes);
        echo "\033[32mCreated:\033[0m Modules/{$name}/Routes/web.php\n";

        $controller = $this->generateController($name);
        file_put_contents($moduleDir . '/Controllers/' . $name . 'Controller.php', $controller);
        echo "\033[32mCreated:\033[0m Modules/{$name}/Controllers/{$name}Controller.php\n";

        $view = $this->generateView($name);
        file_put_contents($moduleDir . '/Views/index.php', $view);
        echo "\033[32mCreated:\033[0m Modules/{$name}/Views/index.php\n";

        $this->registerModule($name);

        echo "\n\033[32mModule '{$name}' created successfully!\033[0m\n";
    }

    protected function generateModuleClass(string $name): string
    {
        $namespace = "Teguh\\Rijanphp\\Modules\\{$name}";

        return <<<PHP
<?php
namespace {$namespace};

class {$name}
{
    public function __construct()
    {
        \\Teguh02\\Rijanphp\\Core\\Modules\\Register::init(
            views: __DIR__ . '/Views',
            routes: __DIR__ . '/Routes',
            models: __DIR__ . '/Models',
            controllers: __DIR__ . '/Controllers',
        );
    }
}
PHP;
    }

    protected function generateRoutes(string $name): string
    {
        $namespace = "Teguh\\Rijanphp\\Modules\\{$name}\\Controllers";

        return <<<PHP
<?php
use Teguh02\Rijanphp\Core\Router\Router;
use {$namespace}\\{$name}Controller;

Router::group(['prefix' => strtolower('{$name}')], function() {
    Router::get('/', [{$name}Controller::class, 'index'])->name('{$name}.index');
});
PHP;
    }

    protected function generateController(string $name): string
    {
        $namespace = "Teguh\\Rijanphp\\Modules\\{$name}\\Controllers";

        return <<<PHP
<?php
namespace {$namespace};

use Teguh02\Rijanphp\Core\Controller\Controller;

class {$name}Controller extends Controller
{
    public function index()
    {
        return view('index', [
            'title' => '{$name} Module'
        ]);
    }
}
PHP;
    }

    protected function generateView(string $name): string
    {
        return <<<PHP
<?php extends_layout('master::layouts/main'); ?>

<?php section('content'); ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4"><?= esc(\$title ?? '{$name}') ?></h1>
    <p>Welcome to the {$name} module.</p>
</div>
<?php end_section(); ?>
PHP;
    }

    protected function registerModule(string $name): void
    {
        $configPath = $this->basePath . 'config/modules.php';

        if (!file_exists($configPath)) {
            return;
        }

        $content = file_get_contents($configPath);

        $newEntry = "\n        \\Teguh02\\Rijanphp\\Modules\\{$name}\\{$name}::class,";

        if (strpos($content, $newEntry) !== false) {
            return;
        }

        $content = str_replace(
            "'modules' => [",
            "'modules' => [{$newEntry}",
            $content
        );

        file_put_contents($configPath, $content);
        echo "\033[32mRegistered:\033[0m Module added to config/modules.php\n";
    }
}
