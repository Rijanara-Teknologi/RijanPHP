<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

class MakeSeederCommand
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
            echo "\033[31mError:\033[0m Seeder name is required.\n";
            echo "Usage: php rijan make:seeder <SeederName>\n";
            exit(1);
        }

        $name = str_replace('Seeder', '', $name);
        $path = $this->basePath . "Master/Seeds/{$name}Seeder.php";

        if (file_exists($path)) {
            echo "\033[31mError:\033[0m Seeder '{$name}Seeder' already exists.\n";
            exit(1);
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $content = $this->generateContent($name);
        file_put_contents($path, $content);

        echo "\033[32mCreated:\033[0m Master/Seeds/{$name}Seeder.php\n";
    }

    protected function generateContent(string $name): string
    {
        return <<<PHP
<?php

namespace Teguh02\Rijanphp\Master\Seeds;

use Teguh02\Rijanphp\Core\Database\Seeder\Seeder;

class {$name}Seeder extends Seeder
{
    public function run()
    {
        //
    }
}
PHP;
    }
}
