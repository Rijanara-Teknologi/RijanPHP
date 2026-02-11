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
    // Detect driver from environment or fallback
    $driver = getenv('DB_CONNECTION') ?: 'sqlite';
    $dbName = getenv('DB_DATABASE') ?: ':memory:';

    // Set environment for config() helper
    putenv("DB_CONNECTION=$driver");
    putenv("DB_DATABASE=$dbName");

    $db = db();

    // Drop tables if they exist (for non-memory dbs)
    $db->query('DROP TABLE IF EXISTS posts');
    $db->query('DROP TABLE IF EXISTS users');

    // Define ID column based on driver
    $idCol = 'id INTEGER PRIMARY KEY AUTOINCREMENT';
    if ($driver === 'mysql') {
        $idCol = 'id INT AUTO_INCREMENT PRIMARY KEY';
    } elseif ($driver === 'pgsql') {
        $idCol = 'id SERIAL PRIMARY KEY';
    }

    // Define column types based on driver
    $dateTimeType = 'DATETIME';
    $boolFalse = '0';
    if ($driver === 'pgsql') {
        $dateTimeType = 'TIMESTAMP';
        $boolFalse = 'FALSE';
    }

    // Create test tables
    $db->query("CREATE TABLE users (
        $idCol,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        age INTEGER,
        created_at $dateTimeType DEFAULT CURRENT_TIMESTAMP,
        updated_at $dateTimeType DEFAULT CURRENT_TIMESTAMP,
        deleted_at $dateTimeType NULL
    )");

    $db->query("CREATE TABLE posts (
        $idCol,
        user_id INTEGER,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        published BOOLEAN DEFAULT $boolFalse,
        created_at $dateTimeType DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

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
