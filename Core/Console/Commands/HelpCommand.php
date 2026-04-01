<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

class HelpCommand
{
    protected $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function handle(array $args)
    {
        echo "\033[32m";
        echo " ╔══════════════════════════════════════════╗\n";
        echo " ║       RijanPHP Framework CLI Tool        ║\n";
        echo " ╚══════════════════════════════════════════╝\n";
        echo "\033[0m\n";

        echo "Usage:\n";
        echo "  php rijan <command> [options] [arguments]\n\n";

        $commands = [
            'Make Commands' => [
                'make:module       <name>' => 'Create a new module',
                'make:controller   <name> [--module=]' => 'Create a new controller',
                'make:model        <name> [--module=]' => 'Create a new model',
                'make:middleware   <name> [--module=]' => 'Create a new middleware',
                'make:migration    <name>' => 'Create a new migration file',
                'make:seeder       <name>' => 'Create a new seeder file',
                'make:view         <name> [--module=]' => 'Create a new view file',
            ],
            'Database Commands' => [
                'migrate' => 'Run all pending migrations',
                'migrate:rollback' => 'Rollback the last batch of migrations',
                'migrate:status' => 'Show migration status',
                'db:seed' => 'Run database seeders',
            ],
            'Utility Commands' => [
                'serve             [--port=]' => 'Start the development server',
                'key:generate' => 'Generate a new application key',
                'route:list' => 'List all registered routes',
                'about' => 'Display framework information',
                'cache:clear' => 'Clear all cached files',
                'help' => 'Show this help message',
            ],
        ];

        foreach ($commands as $category => $cmds) {
            echo "\033[1m{$category}:\033[0m\n";
            foreach ($cmds as $cmd => $desc) {
                echo "  \033[36m{$cmd}\033[0m  {$desc}\n";
            }
            echo "\n";
        }

        echo "\033[1mExamples:\033[0m\n";
        echo "  php rijan make:module Blog\n";
        echo "  php rijan make:controller PostController --module=Blog\n";
        echo "  php rijan make:model Product --module=Shop\n";
        echo "  php rijan make:migration create_posts_table\n";
        echo "  php rijan migrate\n";
        echo "  php rijan serve --port=8080\n";
        echo "  php rijan key:generate\n";
        echo "\n";
    }
}
