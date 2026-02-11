<?php
// Define testing environment
define('RIJANPHP', true);
define('RIJAN_TEST', true);

// Set base path
$basePath = dirname(__DIR__);

// Load autoloader
if (file_exists($basePath . '/vendor/autoload.php')) {
    require $basePath . '/vendor/autoload.php';
} else {
    require $basePath . '/Core/Autoload/Autoloader.php';
}

// Initialize framework
$rijan = new \Teguh02\Rijanphp\Core\Rijan();
$rijan->base_path($basePath)
    ->config($basePath . '/config');

// Setup test database
setupTestDatabase();

function setupTestDatabase()
{
    $dbFile = ':memory:';

    // Configure test database
    $manager = \Teguh02\Rijanphp\Core\Database\DatabaseManager::getInstance();

    // We can't easily inject dynamic config into the manager's config array without a helper or reflection
    // but we can set env vars which the config uses
    putenv("DB_CONNECTION=sqlite");
    putenv("DB_DATABASE=" . $dbFile);

    $db = db();

    // Create test tables
    $db->query('CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        age INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL
    )');

    $db->query('CREATE TABLE posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        published BOOLEAN DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )');

    // Insert seed data
    $db->table('users')->insert([
        ['name' => 'John Doe', 'email' => 'john@example.com', 'age' => 30],
        ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'age' => 25],
        ['name' => 'Bob Johnson', 'email' => 'bob@example.com', 'age' => 35],
    ]);

    $db->table('posts')->insert([
        ['user_id' => 1, 'title' => 'First Post', 'content' => 'Hello World', 'published' => 1],
        ['user_id' => 1, 'title' => 'Second Post', 'content' => 'Another post', 'published' => 0],
        ['user_id' => 2, 'title' => 'Jane\'s Post', 'content' => 'Hi there', 'published' => 1],
    ]);

    return $db;
}
