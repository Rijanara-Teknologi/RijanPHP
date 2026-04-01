<?php
/**
 * Manual Test Script for RijanPHP Security Layer
 * 
 * Run this script directly from the command line:
 * php tests_manual/test_security.php
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

echo "=== RijanPHP Manual Security Tests ===\n\n";

// Test 1: Hash make
test('Hash::make creates a hash', function() {
    $hash = \Teguh02\Rijanphp\Core\Security\Hash::make('password123');
    return !empty($hash) && strlen($hash) > 50;
});

// Test 2: Hash check correct
test('Hash::check verifies correct password', function() {
    $password = 'mypassword';
    $hash = \Teguh02\Rijanphp\Core\Security\Hash::make($password);
    return \Teguh02\Rijanphp\Core\Security\Hash::check($password, $hash);
});

// Test 3: Hash check wrong
test('Hash::check rejects wrong password', function() {
    $hash = \Teguh02\Rijanphp\Core\Security\Hash::make('correct');
    return !\Teguh02\Rijanphp\Core\Security\Hash::check('wrong', $hash);
});

// Test 4: Hash needsRehash
test('Hash::needsRehash works', function() {
    $hash = \Teguh02\Rijanphp\Core\Security\Hash::make('password');
    return is_bool(\Teguh02\Rijanphp\Core\Security\Hash::needsRehash($hash));
});

// Test 5: Encrypter with AES-256
test('Encrypter encrypts and decrypts with AES-256', function() {
    $key = random_bytes(32);
    $encrypter = new \Teguh02\Rijanphp\Core\Security\Encrypter($key);
    $data = 'sensitive data';
    $encrypted = $encrypter->encrypt($data);
    $decrypted = $encrypter->decrypt($encrypted);
    return $data === $decrypted;
});

// Test 6: Encrypter with AES-128
test('Encrypter encrypts and decrypts with AES-128', function() {
    $key = random_bytes(16);
    $encrypter = new \Teguh02\Rijanphp\Core\Security\Encrypter($key, 'AES-128-CBC');
    $data = 'test data';
    $encrypted = $encrypter->encrypt($data);
    $decrypted = $encrypter->decrypt($encrypted);
    return $data === $decrypted;
});

// Test 7: Encrypter different outputs
test('Encrypter produces different outputs for same input', function() {
    $key = random_bytes(32);
    $encrypter = new \Teguh02\Rijanphp\Core\Security\Encrypter($key);
    $enc1 = $encrypter->encrypt('same');
    $enc2 = $encrypter->encrypt('same');
    return $enc1 !== $enc2;
});

// Test 8: Encrypter wrong key
test('Encrypter fails with wrong key', function() {
    $key1 = random_bytes(32);
    $key2 = random_bytes(32);
    $enc1 = new \Teguh02\Rijanphp\Core\Security\Encrypter($key1);
    $enc2 = new \Teguh02\Rijanphp\Core\Security\Encrypter($key2);
    $encrypted = $enc1->encrypt('secret');
    try {
        $enc2->decrypt($encrypted);
        return false;
    } catch (Exception $e) {
        return true;
    }
});

// Test 9: Encrypter invalid data
test('Encrypter fails with invalid data', function() {
    $key = random_bytes(32);
    $encrypter = new \Teguh02\Rijanphp\Core\Security\Encrypter($key);
    try {
        $encrypter->decrypt('not-valid-encrypted-data');
        return false;
    } catch (Exception $e) {
        return true;
    }
});

// Test 10: Encrypter with array
test('Encrypter handles array data', function() {
    $key = random_bytes(32);
    $encrypter = new \Teguh02\Rijanphp\Core\Security\Encrypter($key);
    $data = ['name' => 'John', 'email' => 'john@example.com'];
    $decrypted = $encrypter->decrypt($encrypter->encrypt($data));
    return $data === $decrypted;
});

// Test 11: Encrypter with object
test('Encrypter handles object data', function() {
    $key = random_bytes(32);
    $encrypter = new \Teguh02\Rijanphp\Core\Security\Encrypter($key);
    $data = new stdClass();
    $data->id = 1;
    $data->name = 'Test';
    $decrypted = $encrypter->decrypt($encrypter->encrypt($data));
    return $data == $decrypted;
});

// Test 12: Encrypter with null
test('Encrypter handles null data', function() {
    $key = random_bytes(32);
    $encrypter = new \Teguh02\Rijanphp\Core\Security\Encrypter($key);
    $decrypted = $encrypter->decrypt($encrypter->encrypt(null));
    return $decrypted === null;
});

// Test 13: Encrypter with empty string
test('Encrypter handles empty string', function() {
    $key = random_bytes(32);
    $encrypter = new \Teguh02\Rijanphp\Core\Security\Encrypter($key);
    $decrypted = $encrypter->decrypt($encrypter->encrypt(''));
    return $decrypted === '';
});

// Test 14: Encrypter with numbers
test('Encrypter handles numeric data', function() {
    $key = random_bytes(32);
    $encrypter = new \Teguh02\Rijanphp\Core\Security\Encrypter($key);
    $decrypted = $encrypter->decrypt($encrypter->encrypt(12345));
    return $decrypted === 12345;
});

// Test 15: Encrypter with boolean
test('Encrypter handles boolean data', function() {
    $key = random_bytes(32);
    $encrypter = new \Teguh02\Rijanphp\Core\Security\Encrypter($key);
    $decrypted = $encrypter->decrypt($encrypter->encrypt(true));
    return $decrypted === true;
});

// Test 16: Encrypter short key validation
test('Encrypter rejects short keys', function() {
    try {
        new \Teguh02\Rijanphp\Core\Security\Encrypter('short');
        return false;
    } catch (Exception $e) {
        return true;
    }
});

// Test 17: CSRF generate
test('CSRF generates token', function() {
    \Teguh02\Rijanphp\Core\Session\Session::start();
    $token = \Teguh02\Rijanphp\Core\Security\Csrf::generate();
    return strlen($token) === 64;
});

// Test 18: CSRF verify
test('CSRF verifies correct token', function() {
    \Teguh02\Rijanphp\Core\Session\Session::start();
    $token = \Teguh02\Rijanphp\Core\Security\Csrf::generate();
    return \Teguh02\Rijanphp\Core\Security\Csrf::verify($token);
});

// Test 19: CSRF reject wrong token
test('CSRF rejects wrong token', function() {
    \Teguh02\Rijanphp\Core\Session\Session::start();
    \Teguh02\Rijanphp\Core\Security\Csrf::generate();
    return !\Teguh02\Rijanphp\Core\Security\Csrf::verify('wrong-token');
});

// Test 20: CSRF token returns existing
test('CSRF token returns existing token', function() {
    \Teguh02\Rijanphp\Core\Session\Session::start();
    $token1 = \Teguh02\Rijanphp\Core\Security\Csrf::generate();
    $token2 = \Teguh02\Rijanphp\Core\Security\Csrf::token();
    return $token1 === $token2;
});

echo "\n=== Results ===\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
echo "Total: " . ($passed + $failed) . "\n";

exit($failed > 0 ? 1 : 0);
