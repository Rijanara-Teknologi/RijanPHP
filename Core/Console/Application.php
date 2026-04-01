<?php

namespace Teguh02\Rijanphp\Core\Console;

use Teguh02\Rijanphp\Core\Console\Commands\AboutCommand;
use Teguh02\Rijanphp\Core\Console\Commands\CacheClearCommand;
use Teguh02\Rijanphp\Core\Console\Commands\DbSeedCommand;
use Teguh02\Rijanphp\Core\Console\Commands\KeyGenerateCommand;
use Teguh02\Rijanphp\Core\Console\Commands\MakeControllerCommand;
use Teguh02\Rijanphp\Core\Console\Commands\MakeMiddlewareCommand;
use Teguh02\Rijanphp\Core\Console\Commands\MakeMigrationCommand;
use Teguh02\Rijanphp\Core\Console\Commands\MakeModelCommand;
use Teguh02\Rijanphp\Core\Console\Commands\MakeModuleCommand;
use Teguh02\Rijanphp\Core\Console\Commands\MakeSeederCommand;
use Teguh02\Rijanphp\Core\Console\Commands\MakeViewCommand;
use Teguh02\Rijanphp\Core\Console\Commands\MigrateCommand;
use Teguh02\Rijanphp\Core\Console\Commands\MigrateRollbackCommand;
use Teguh02\Rijanphp\Core\Console\Commands\MigrateStatusCommand;
use Teguh02\Rijanphp\Core\Console\Commands\RouteListCommand;
use Teguh02\Rijanphp\Core\Console\Commands\ServeCommand;

class Application
{
    protected $basePath;
    protected $commands = [];
    protected $defaultCommand = 'help';

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR;

        $this->registerCommands();
    }

    protected function registerCommands()
    {
        $this->commands = [
            'help' => [
                'class' => \Teguh02\Rijanphp\Core\Console\Commands\HelpCommand::class,
                'description' => 'Show help information',
            ],
            'about' => [
                'class' => AboutCommand::class,
                'description' => 'Display framework information',
            ],
            'make:module' => [
                'class' => MakeModuleCommand::class,
                'description' => 'Create a new module',
            ],
            'make:controller' => [
                'class' => MakeControllerCommand::class,
                'description' => 'Create a new controller',
            ],
            'make:model' => [
                'class' => MakeModelCommand::class,
                'description' => 'Create a new model',
            ],
            'make:middleware' => [
                'class' => MakeMiddlewareCommand::class,
                'description' => 'Create a new middleware',
            ],
            'make:migration' => [
                'class' => MakeMigrationCommand::class,
                'description' => 'Create a new migration file',
            ],
            'make:seeder' => [
                'class' => MakeSeederCommand::class,
                'description' => 'Create a new seeder file',
            ],
            'make:view' => [
                'class' => MakeViewCommand::class,
                'description' => 'Create a new view file',
            ],
            'migrate' => [
                'class' => MigrateCommand::class,
                'description' => 'Run all pending migrations',
            ],
            'migrate:rollback' => [
                'class' => MigrateRollbackCommand::class,
                'description' => 'Rollback the last batch of migrations',
            ],
            'migrate:status' => [
                'class' => MigrateStatusCommand::class,
                'description' => 'Show migration status',
            ],
            'db:seed' => [
                'class' => DbSeedCommand::class,
                'description' => 'Run database seeders',
            ],
            'serve' => [
                'class' => ServeCommand::class,
                'description' => 'Start the development server',
            ],
            'key:generate' => [
                'class' => KeyGenerateCommand::class,
                'description' => 'Generate a new application key',
            ],
            'route:list' => [
                'class' => RouteListCommand::class,
                'description' => 'List all registered routes',
            ],
            'cache:clear' => [
                'class' => CacheClearCommand::class,
                'description' => 'Clear all cached files',
            ],
        ];
    }

    public function run()
    {
        $argv = $_SERVER['argv'] ?? [];
        array_shift($argv);

        $commandName = $argv[0] ?? $this->defaultCommand;
        $arguments = array_slice($argv, 1);

        if (!isset($this->commands[$commandName])) {
            $this->error("Command '{$commandName}' is not defined.");
            $this->line('');
            $this->line('Run <info>php rijan help</info> for available commands.');
            exit(1);
        }

        $commandClass = $this->commands[$commandName]['class'];
        $command = new $commandClass($this->basePath);
        $command->handle($arguments);
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    public function info(string $message)
    {
        $this->line("\033[32m{$message}\033[0m");
    }

    public function error(string $message)
    {
        $this->line("\033[31m{$message}\033[0m");
    }

    public function warning(string $message)
    {
        $this->line("\033[33m{$message}\033[0m");
    }

    public function comment(string $message)
    {
        $this->line("\033[36m{$message}\033[0m");
    }

    public function line(string $message = '')
    {
        echo $message . PHP_EOL;
    }

    public function newLine(int $count = 1)
    {
        echo str_repeat(PHP_EOL, $count);
    }

    public function table(array $headers, array $rows)
    {
        $widths = array_map('strlen', $headers);

        foreach ($rows as $row) {
            foreach ($row as $i => $cell) {
                $widths[$i] = max($widths[$i] ?? 0, strlen($cell));
            }
        }

        $headerLine = $this->buildRow($headers, $widths);
        $separator = $this->buildSeparator($widths);

        $this->line($separator);
        $this->line($headerLine);
        $this->line($separator);

        foreach ($rows as $row) {
            $this->line($this->buildRow($row, $widths));
        }

        $this->line($separator);
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
            $parts[] = '-' . str_repeat('-', $width) . '-';
        }
        return '+' . implode('+', $parts) . '+';
    }

    public function confirm(string $question): bool
    {
        echo $question . ' (yes/no) [' . "\033[32myes\033[0m]: ";
        $input = trim(fgets(STDIN));
        return empty($input) || strtolower($input) === 'y' || strtolower($input) === 'yes';
    }

    public function ask(string $question, $default = null)
    {
        $defaultText = $default !== null ? " [{$default}]" : '';
        echo $question . $defaultText . ': ';
        $input = trim(fgets(STDIN));
        return $input === '' ? $default : $input;
    }

    public function secret(string $question)
    {
        echo $question . ': ';

        if (PHP_OS_FAMILY === 'Windows') {
            $vbsScript = tempnam(sys_get_temp_dir(), 'vbs');
            file_put_contents($vbsScript, "WScript.Echo CreateObject(\"Scripting.FileSystemObject\").GetStandardStream(1).ReadLine()");
            $password = shell_exec("cscript //nologo " . escapeshellarg($vbsScript));
            unlink($vbsScript);
            return trim($password);
        }

        return trim(shell_exec('read -s password; echo $password'));
    }
}
