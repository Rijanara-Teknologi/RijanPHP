# RijanPHP Framework

**RijanPHP** is a modern, modular, and lightweight PHP framework designed for developers who value simplicity, autonomy, and performance. 

## 🚀 Core Philosophy

### 1. True Modularity
RijanPHP is built around self-contained modules. Each feature is encapsulated with its own routes, controllers, views, and models.

### 2. Native & Independent
Prioritizes core PHP power. RijanPHP runs efficiently without mandatory third-party dependencies, featuring a native autoloader and a zero-compilation view engine.

### 3. CI-Style Developer Experience
Familiar patterns inspired by CodeIgniter 4, including fluent Models, easy CRUD, and powerful database abstractions.

## 🛠 Features

- **Advanced Routing**: Route groups, middleware stacking, and named routes.
- **Native View Engine**: Maximum performance with layout inheritance and sections.
- **Security Suite**: Bcrypt Hashing, AES-256-CBC Encryption, and CSRF Protection.
- **Storage System**: Unified file system abstraction with local disk support.
- **HTTP Client**: Built-in, fluent HTTP client for external API requests.
- **CI-Style Models**: Fluent CRUD, Soft Deletes, automatic Timestamps, and Lifecycle Callbacks.
- **Multi-Database Support**: Support for MySQL/MariaDB, PostgreSQL, and SQLite with a unified PDO interface.
- **PSR-3 Logging**: Multi-channel logging with automatic request tracking.
- **Request/Response Objects**: Centralized handling of HTTP inputs and outputs.
- **Testing Suite**: Integrated PHPUnit 10 support with `Feature` and `Unit` testing capabilities.

## 📦 Directory Structure

```
rijanphp/
├── Core/               # Framework Core (HTTP, Router, Model, Database, etc.)
├── Master/             # Base classes and components
├── Modules/            # Application Modules
│   └── Homepage/       # Example Module
├── storage/            # Logs, database files, and caches
├── config/             # Environment-aware configuration
└── index.php           # Entry point
```

## 🚦 Usage Examples

### Enhanced Routing
```php
Router::group(['prefix' => 'api', 'middleware' => 'auth'], function() {
    Router::get('users', [UserController::class, 'index'])->name('api.users');
});
```

### CI-Style Model
```php
class UserModel extends Model {
    protected $table = 'users';
    protected $allowedFields = ['name', 'email'];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
}

$user = model(UserModel::class)->find(1);
```

### Native View System
```php
// In layout.php
echo $this->yield('content');

// In view.php
<?php extends_layout('layout'); ?>
<?php section('content'); ?>
    <h1>Welcome to RijanPHP</h1>
<?php end_section(); ?>
```

### Multi-Database Helper
```php
// Use SQLite easily
'sqlite' => [
    'driver' => 'sqlite',
    'database' => database_path('app.sqlite'),
],
```

### Security & Encryption
```php
// Hashing
$hash = Hash::make('password');
if (Hash::check('password', $hash)) { ... }

// Encryption (uses APP_KEY)
$encrypted = encrypt('secret data');
$decrypted = decrypt($encrypted);
```

### Storage Management
```php
// Store file
storage()->put('avatars/user.jpg', $content);

// Get URL
echo storage()->url('avatars/user.jpg');
```

### HTTP Client
```php
$response = http()->get('https://api.example.com/users');
if ($response->successful()) {
    $users = $response->json();
}
```

## 🚥 Global Helpers
- `view()`, `route()`, `config()`, `request()`, `response()`, `redirect()`, `back()`, `db()`, `model()`, `log_info()`.
- `storage()`, `http()`, `encrypt()`, `decrypt()`, `bcrypt()`.

## 🚥 Requirements
- PHP 8.0+
- PDO Extensions for your chosen database.

## 📄 License
[MIT License](LICENSE)
