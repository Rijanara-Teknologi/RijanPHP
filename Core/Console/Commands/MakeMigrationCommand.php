<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

class MakeMigrationCommand
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
            echo "\033[31mError:\033[0m Migration name is required.\n";
            echo "Usage: php rijan make:migration <create_table_name_table>\n";
            echo "Example: php rijan make:migration create_posts_table\n";
            exit(1);
        }

        $timestamp = date('Y_m_d_His');
        $filename = $timestamp . '_' . $name . '.php';
        $path = $this->basePath . 'Master/Migrations/' . $filename;

        if (file_exists($path)) {
            echo "\033[31mError:\033[0m Migration '{$filename}' already exists.\n";
            exit(1);
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $tableName = $this->extractTableName($name);
        $className = $this->toClassName($name);
        $isCreate = strpos(strtolower($name), 'create_') === 0;

        $content = $this->generateContent($className, $tableName, $isCreate);
        file_put_contents($path, $content);

        echo "\033[32mCreated:\033[0m Master/Migrations/{$filename}\n";
    }

    protected function extractTableName(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/^create_/', '', $name);
        $name = preg_replace('/_table$/', '', $name);
        $name = preg_replace('/^add_.*_to_/', '', $name);
        return $name;
    }

    protected function toClassName(string $name): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    }

    protected function generateContent(string $className, string $tableName, bool $isCreate): string
    {
        if ($isCreate) {
            return <<<PHP
<?php

use Teguh02\Rijanphp\Core\Database\Migration\Migration;
use Teguh02\Rijanphp\Core\Database\Schema\Blueprint;
use Teguh02\Rijanphp\Core\Database\Schema\Schema;

class {$className} extends Migration
{
    public function up()
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('{$tableName}');
    }
}
PHP;
        }

        return <<<PHP
<?php

use Teguh02\Rijanphp\Core\Database\Migration\Migration;
use Teguh02\Rijanphp\Core\Database\Schema\Blueprint;
use Teguh02\Rijanphp\Core\Database\Schema\Schema;

class {$className} extends Migration
{
    public function up()
    {
        Schema::table('{$tableName}', function (Blueprint \$table) {
            //
        });
    }

    public function down()
    {
        Schema::table('{$tableName}', function (Blueprint \$table) {
            //
        });
    }
}
PHP;
    }
}
