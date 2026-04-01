<?php
/**
 * Manual Test Script for RijanPHP Framework
 * 
 * Run this script directly from the command line:
 * php tests_manual/test_core.php
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

echo "=== RijanPHP Manual Core Tests ===\n\n";

// Test 1: Autoloader
test('Autoloader loads classes', function() {
    return class_exists(\Teguh02\Rijanphp\Core\Rijan::class);
});

// Test 2: Version
test('Framework version is defined', function() {
    return !empty(\Teguh02\Rijanphp\Core\Rijan::version());
});

// Test 3: Config loading
test('Config can be loaded', function() {
    $config = config('app');
    return is_array($config) && isset($config['name']);
});

// Test 4: Environment variable
test('Environment variable helper works', function() {
    $env = env('APP_ENV', 'production');
    return is_string($env);
});

// Test 5: Base path
test('Base path helper works', function() {
    $path = base_path();
    return !empty($path) && is_dir($path);
});

// Test 6: Router
test('Router can register routes', function() {
    \Teguh02\Rijanphp\Core\Router\Router::clear();
    \Teguh02\Rijanphp\Core\Router\Router::get('test', function() {
        return 'test';
    });
    $routes = \Teguh02\Rijanphp\Core\Router\Router::getRoutes();
    return isset($routes['GET']);
});

// Test 7: Hash
test('Hash can create and verify passwords', function() {
    $password = 'testpassword';
    $hash = \Teguh02\Rijanphp\Core\Security\Hash::make($password);
    return \Teguh02\Rijanphp\Core\Security\Hash::check($password, $hash);
});

// Test 8: Encrypter
test('Encrypter can encrypt and decrypt', function() {
    $key = random_bytes(32);
    $encrypter = new \Teguh02\Rijanphp\Core\Security\Encrypter($key);
    $data = 'secret data';
    $encrypted = $encrypter->encrypt($data);
    $decrypted = $encrypter->decrypt($encrypted);
    return $data === $decrypted;
});

// Test 9: View Engine
test('View engine can be instantiated', function() {
    $engine = \Teguh02\Rijanphp\Core\View\View::getEngine();
    return $engine instanceof \Teguh02\Rijanphp\Core\View\ViewEngine;
});

// Test 10: Database Manager
test('Database manager can be initialized', function() {
    try {
        $db = \Teguh02\Rijanphp\Core\Database\DatabaseManager::getInstance();
        return $db !== null;
    } catch (Exception $e) {
        return false;
    }
});

// Test 11: HTTP Request
test('Request can be instantiated', function() {
    $request = new \Teguh02\Rijanphp\Core\Http\Request();
    return $request instanceof \Teguh02\Rijanphp\Core\Http\Request;
});

// Test 12: HTTP Response
test('Response can be created', function() {
    $response = new \Teguh02\Rijanphp\Core\Http\Response();
    $response->setContent('test');
    return $response->getContent() === 'test';
});

// Test 13: Storage path
test('Storage path helper works', function() {
    $path = storage_path();
    return !empty($path);
});

// Test 14: Log Manager
test('Log manager can be initialized', function() {
    try {
        $log = \Teguh02\Rijanphp\Core\Log\LogManager::getInstance();
        return $log !== null;
    } catch (Exception $e) {
        return false;
    }
});

echo "\n=== Results ===\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
echo "Total: " . ($passed + $failed) . "\n";

exit($failed > 0 ? 1 : 0);
