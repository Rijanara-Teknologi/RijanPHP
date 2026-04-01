<?php
/**
 * Manual Test Script for RijanPHP Database Layer
 * 
 * Run this script directly from the command line:
 * php tests_manual/test_database.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('RIJANPHP', true);
$base_path = dirname(__DIR__) . '/';

require $base_path . 'Core/Autoload/Autoloader.php';

$rijan = (new \Teguh02\Rijanphp\Core\Rijan())
    ->base_path($base_path)
    ->config($base_path . 'config');

$passed = 0;
$failed = 0;

function test($name, $callback) {
    global $passed, $failed;
    try {
        $result = $callback();
        if ($result) {
            echo "✅ PASS: $name\n";
            $passed++;
        } else {
            echo "❌ FAIL: $name\n";
            $failed++;
        }
    } catch (Exception $e) {
        echo "❌ FAIL: $name - " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "=== RijanPHP Manual Database Tests ===\n\n";

// Test 1: Database Manager
test('Database manager initializes', function() {
    $db = \Teguh02\Rijanphp\Core\Database\DatabaseManager::getInstance();
    return $db !== null;
});

// Test 2: Default connection
test('Default connection is SQLite', function() {
    $config = config('database');
    return $config['default'] === 'sqlite';
});

// Test 3: Connection config exists
test('SQLite connection config exists', function() {
    $config = config('database');
    return isset($config['connections']['sqlite']);
});

// Test 4: Query Builder instantiation
test('Query builder can be created', function() {
    $builder = db();
    return $builder instanceof \Teguh02\Rijanphp\Core\Database\QueryBuilder;
});

// Test 5: Query Builder table method
test('Query builder table method works', function() {
    $builder = db()->table('test_table');
    return $builder instanceof \Teguh02\Rijanphp\Core\Database\QueryBuilder;
});

// Test 6: Schema Blueprint
test('Schema Blueprint can be created', function() {
    $blueprint = new \Teguh02\Rijanphp\Core\Database\Schema\Blueprint('users');
    return $blueprint !== null;
});

// Test 7: Blueprint ID column
test('Blueprint generates ID column', function() {
    $blueprint = new \Teguh02\Rijanphp\Core\Database\Schema\Blueprint('users');
    $blueprint->id();
    $sql = $blueprint->toSql();
    return strpos($sql, 'id') !== false;
});

// Test 8: Blueprint timestamps
test('Blueprint generates timestamps', function() {
    $blueprint = new \Teguh02\Rijanphp\Core\Database\Schema\Blueprint('users');
    $blueprint->timestamps();
    $sql = $blueprint->toSql();
    return strpos($sql, 'created_at') !== false && strpos($sql, 'updated_at') !== false;
});

// Test 9: Blueprint string column
test('Blueprint generates string column', function() {
    $blueprint = new \Teguh02\Rijanphp\Core\Database\Schema\Blueprint('users');
    $blueprint->string('name');
    $sql = $blueprint->toSql();
    return strpos($sql, 'name') !== false;
});

// Test 10: Blueprint complete table
test('Blueprint generates complete CREATE TABLE SQL', function() {
    $blueprint = new \Teguh02\Rijanphp\Core\Database\Schema\Blueprint('products');
    $blueprint->id();
    $blueprint->string('name');
    $blueprint->text('description');
    $blueprint->timestamps();
    $sql = $blueprint->toSql();
    return strpos($sql, 'CREATE TABLE') !== false && strpos($sql, 'products') !== false;
});

// Test 11: Migration class exists
test('Migration base class exists', function() {
    return class_exists(\Teguh02\Rijanphp\Core\Database\Migration\Migration::class);
});

// Test 12: Seeder class exists
test('Seeder base class exists', function() {
    return class_exists(\Teguh02\Rijanphp\Core\Database\Seeder\Seeder::class);
});

// Test 13: Migrator class exists
test('Migrator class exists', function() {
    return class_exists(\Teguh02\Rijanphp\Core\Database\Migration\Migrator::class);
});

// Test 14: PDO Connection class exists
test('PDO Connection class exists', function() {
    return class_exists(\Teguh02\Rijanphp\Core\Database\PdoConnection::class);
});

// Test 15: MySQL Connection class exists
test('MySQL Connection class exists', function() {
    return class_exists(\Teguh02\Rijanphp\Core\Database\Connections\MySqlConnection::class);
});

echo "\n=== Results ===\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
echo "Total: " . ($passed + $failed) . "\n";

exit($failed > 0 ? 1 : 0);
