<?php

// Define testing constants
define('RIJANPHP', true);
define('RIJAN_TEST', true);
define('BASE_PATH', dirname(__DIR__));

// Load Autoloader directly (No Vendor)
require BASE_PATH . '/Core/Autoload/Autoloader.php';

// Get Autoloader instance
$loader = \Teguh02\Rijanphp\Core\Autoload\Autoloader::getInstance();

// Manually register namespaces (Simulating composer.json autoload)
$loader->addNamespace('Teguh02\\Rijanphp\\', BASE_PATH . '/');
$loader->addNamespace('Teguh02\\Rijanphp\\Master\\', BASE_PATH . '/Master');
$loader->addNamespace('Teguh02\\Rijanphp\\Modules\\', BASE_PATH . '/Modules');

// Initialize App (Minimal)
use Teguh02\Rijanphp\Core\Rijan;
$app = new Rijan();
$app->base_path(BASE_PATH);
$app->config(BASE_PATH . '/config'); // Fix: Set config path

// Setup Database Connection (SQLite Memory)
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=:memory:');
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = ':memory:';

// Helper function to get DB connection
if (!function_exists('db')) {
    function db()
    {
        return \Teguh02\Rijanphp\Core\Database\DatabaseManager::getInstance();
    }
}

// Setup Database Schema and Seed
function setupManualDatabase()
{
    $db = db();

    $db->query("DROP TABLE IF EXISTS posts");
    $db->query("DROP TABLE IF EXISTS users");

    // Users Table
    $db->query("CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        age INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL
    )");

    // Posts Table
    $db->query("CREATE TABLE posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        published BOOLEAN DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Seed Users
    $db->table('users')->insert([
        ['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'secret', 'age' => 30],
        ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'password' => 'secret', 'age' => 25],
        ['name' => 'Bob Johnson', 'email' => 'bob@example.com', 'password' => 'secret', 'age' => 35],
    ]);

    // Seed Posts
    $db->table('posts')->insert([
        ['user_id' => 1, 'title' => 'First Post', 'content' => 'Hello World', 'published' => 1],
        ['user_id' => 1, 'title' => 'Second Post', 'content' => 'Another post', 'published' => 0],
        ['user_id' => 2, 'title' => 'Jane\'s Post', 'content' => 'Hi there', 'published' => 1],
    ]);
}
