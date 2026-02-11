# Changelog

All notable changes to the **RijanPHP** framework will be documented in this file.

## [1.3.0] - 2026-02-11

### Added
- **HTTP & Controller Layer**:
  - Implemented `Core\Http\Request` for centralized input (`GET`, `POST`, `FILES`, JSON) and server data handling.
  - Implemented `Core\Http\Response` for standardized output (JSON, Redirect, HTML status).
  - Added base `Core\Controller\Controller` with Request injection and utility methods.
  - Added global HTTP helpers: `request()`, `response()`, `redirect()`, `back()`.
- **HTTP Client Library**:
  - Implemented a minimalist, Guzzle-inspired HTTP client (`Core\Http\Client`) using PHP cURL.
  - Supports standard HTTP methods: `get()`, `post()`, `put()`, `patch()`, `delete()`.
  - Feature-rich request options: `json`, `form_params`, `query`, `headers`, `auth`, `timeout`.
  - Standardized response handling via `Core\Http\ClientResponse` with `json()`, `status()`, and `successful()` utilities.
  - Added global `http()` helper function for fluent API interaction.
- **Security & Storage Services**:
  - Implemented `Core\Storage\Storage` for Laravel-style file management with local disk support.
  - Implemented `Core\Security\Hash` for secure Bcrypt password hashing.
  - Implemented `Core\Security\Encrypter` for AES-256-CBC encryption using `APP_KEY`.
  - Added global helpers: `storage()`, `bcrypt()`, `hash_make()`, `encrypt()`, `decrypt()`.
  - Refactored `CookieManager` to use the centralized `Encrypter` service.
- **Advanced Routing**:
  - Added **Route Groups** support for prefixing and middleware shared across multiple routes.
  - Implemented **Route-Level Middleware Execution** in `Router` to enforce security policies.
  - Implemented Middleware stacking for routes and groups.
  - Enhanced Router dispatching to inject `Request` and capture `Response` objects.
- **Multi-Database Support**:
  - Refactored database connections to use a driver-agnostic `Core\Database\PdoConnection` base class.
  - Added support for **PostgreSQL** via `PgSqlConnection`.
  - Added support for **SQLite** via `SqliteConnection`.
  - Set **SQLite** as the default database connection for easier local development.
  - Added `database_path($path)` helper for SQLite management.
  - Added `db()->close()` for manual connection termination.
- **Environment & Configuration**:
  - Implemented automatic **.env file loading** in `Core\Rijan`.
  - Enhanced `env()` helper with support for default values and type casting (boolean, null, empty).
  - Updated all core configuration files to utilize `env()` with sensible fallback defaults.
- **Testing Suite**:
  - Integrated **PHPUnit 10** for comprehensive unit and feature testing.
  - Implemented automated **SQLite-in-memory** testing environment.
  - Added support for **Multi-row Inserts** in `QueryBuilder`.
  - Fixed various core bugs in `Request`, `Response`, and `Database` revealed by the testing suite.
  - Added `Makefile` for developer workflow and **GitHub Actions** for CI/CD.

- **Error Handling & Professional Pages**:
  - Implemented `Core\Exception\Handler` for global, high-reliability error and exception management.
  - Added minimalist and professional **404 Page Not Found** view with Tailwind CSS.
  - Added premium **500 Server Error** view with dynamic **Debug Mode** support.
  - Feature: **Copy Error** button in 500 debug view to copy stack traces and exception details to clipboard.
  - Automated output buffer clearing and view state resetting in `Handler::render` for stable error reporting even within crashed views.
  - Registered `core::` view namespace for framework internal assets.

### Changed
- **Lifecycle Refactor**:
  - Updated `Core\Rijan::run` to manage the complete Request-Response loop and bootstrap the global error handler.
  - Standardized PDO fetch mode to `FETCH_ASSOC` across drivers for consistency.
- **Directory Structure & PSR-4 Compliance**:
  - Renamed root directories to PascalCase: `core` -> `Core`, `master` -> `Master`, `modules` -> `Modules`.
  - Updated namespace declarations across the framework to match PSR-4 standards (e.g., `Teguh02\Rijanphp\Core`).
- **Module Registration**:
  - Updated `Core\Modules\Register::init` to automatically load `routes/web.php` from modules.
  - Updated `Core\Modules\Executor::Run` to bootstrap modules via config before dispatching routes.
- **Modular View System**:
  - Implemented **Context-Aware View Namespacing** to resolve naming collisions between modules.
  - Added support for `::` namespace notation in `ViewEngine` and `View` classes.
  - Implemented **Automatic Namespace Detection** in `Register::init` using `debug_backtrace`.

### Fixed
- **Stability**:
  - Added defensive checks in `LogManager` and `VerifyCsrfToken` to prevent crashes during early request lifecycle.
  - Fixed 500 error page rendering failures by automatically clearing output buffers and resetting `View` state.
- **Routing**:
  - Resolved route and view collision between `Homepage` and `Product` modules where the root route `/` was shadowed by module views.
  - Fixed 404 error caused by modules not being bootstrapped before routing.
- Resolved "Access denied" string issues in CLI environment by ensuring the `RIJANPHP` protection is appropriately handled.

## [1.0.0] - Initial Release
- Basic MVC structure.
- Initial Autoloader implementation.
