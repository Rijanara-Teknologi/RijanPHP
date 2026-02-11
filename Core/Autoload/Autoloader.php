<?php
/**
 * RijanPHP Autoloader
 * 
 * A smart PSR-4 compliant autoloader that automatically reads configuration
 * from composer.json to ensure seamless transition between custom autoloader
 * and Composer's autoloader.
 * 
 * This autoloader is specifically designed for modular framework structure
 * with core, master, and modules directories.
 * 
 * @package Teguh02\Rijanphp\Core\Autoload
 * @author Teguh Rijanandi
 * @version 2.0
 */

namespace Teguh02\Rijanphp\Core\Autoload;

class Autoloader
{
    /**
     * Array of namespace prefixes and their corresponding base directories
     * Format: ['Namespace\\Prefix\\' => ['/path/to/directory1', '/path/to/directory2']]
     * 
     * @var array<string, array<string>>
     */
    private array $prefixes = [];

    /**
     * Path to composer.json file
     * 
     * @var string|null
     */
    private ?string $composerJsonPath = null;

    /**
     * Base path of the framework
     * 
     * @var string
     */
    private string $basePath;

    /**
     * Constructor - automatically registers the autoloader
     * 
     * @param string|null $basePath Optional base path, defaults to framework root
     */
    public function __construct(?string $basePath = null)
    {
        // Set base path (framework root directory)
        $this->basePath = $basePath ?? dirname(__DIR__, 2);

        // Register this autoloader with PHP's autoloading mechanism
        spl_autoload_register([$this, 'loadClass'], true, true);

        // Load PSR-4 mappings from composer.json
        $this->loadComposerConfig();

        // Register additional default namespaces for RijanPHP
        $this->registerDefaultNamespaces();
    }

    /**
     * Load PSR-4 namespace mappings from composer.json
     * This ensures compatibility with Composer's autoloader
     * 
     * @return bool Returns true if composer.json was loaded successfully
     */
    private function loadComposerConfig(): bool
    {
        $composerJsonPath = $this->basePath . DIRECTORY_SEPARATOR . 'composer.json';

        // Check if composer.json exists
        if (!file_exists($composerJsonPath)) {
            trigger_error("composer.json not found at {$composerJsonPath}", E_USER_WARNING);
            return false;
        }

        // Read and parse composer.json
        $composerJsonContent = file_get_contents($composerJsonPath);
        $composerConfig = json_decode($composerJsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            trigger_error("Failed to parse composer.json: " . json_last_error_msg(), E_USER_WARNING);
            return false;
        }

        // Check if autoload configuration exists
        if (!isset($composerConfig['autoload']['psr-4'])) {
            trigger_error("No PSR-4 autoload configuration found in composer.json", E_USER_WARNING);
            return false;
        }

        // Register all PSR-4 namespace mappings from composer.json
        $psr4Mappings = $composerConfig['autoload']['psr-4'];

        foreach ($psr4Mappings as $namespace => $path) {
            // Convert relative path to absolute path
            $absolutePath = $this->basePath . DIRECTORY_SEPARATOR . $path;

            // Register the namespace
            $this->addNamespace($namespace, $absolutePath);
        }

        // Store composer.json path for reference
        $this->composerJsonPath = $composerJsonPath;

        return true;
    }

    /**
     * Register default RijanPHP namespaces for modular structure
     * This complements the composer.json configuration
     */
    private function registerDefaultNamespaces(): void
    {
        // Register core namespace (already in composer.json, but ensure it's loaded)
        $corePath = $this->basePath . DIRECTORY_SEPARATOR . 'Core';
        $this->addNamespace('Teguh02\\Rijanphp\\Core\\', $corePath);

        // Register master namespace for global shared code
        $masterPath = $this->basePath . DIRECTORY_SEPARATOR . 'Master';
        if (is_dir($masterPath)) {
            $this->addNamespace('Rijanphp\\Master\\', $masterPath);
        }

        // Register modules namespace for modular structure
        $modulesPath = $this->basePath . DIRECTORY_SEPARATOR . 'Modules';
        if (is_dir($modulesPath)) {
            $this->addNamespace('Rijanphp\\Modules\\', $modulesPath);
        }

        // Register additional common directories within core
        $additionalCoreDirs = [
            'Autoload\\' => $corePath . DIRECTORY_SEPARATOR . 'autoload',
            'Helpers\\' => $corePath . DIRECTORY_SEPARATOR . 'helpers',
            'Middleware\\' => $corePath . DIRECTORY_SEPARATOR . 'middleware',
            'Modules\\' => $corePath . DIRECTORY_SEPARATOR . 'modules',
            'Views\\' => $corePath . DIRECTORY_SEPARATOR . 'views',
        ];

        foreach ($additionalCoreDirs as $namespace => $dir) {
            if (is_dir($dir)) {
                $this->addNamespace('Teguh02\\Rijanphp\\Core\\' . $namespace, $dir);
            }
        }

        // Register master subdirectories
        $masterSubdirs = [
            'Helpers\\' => $masterPath . DIRECTORY_SEPARATOR . 'helpers',
            'Middleware\\' => $masterPath . DIRECTORY_SEPARATOR . 'middleware',
            'Migrations\\' => $masterPath . DIRECTORY_SEPARATOR . 'migrations',
            'Models\\' => $masterPath . DIRECTORY_SEPARATOR . 'models',
            'Views\\' => $masterPath . DIRECTORY_SEPARATOR . 'views',
        ];

        foreach ($masterSubdirs as $namespace => $dir) {
            if (is_dir($dir)) {
                $this->addNamespace('Rijanphp\\Master\\' . $namespace, $dir);
            }
        }
    }

    /**
     * Register a namespace prefix with its base directory
     * 
     * @param string $prefix The namespace prefix (e.g., 'App\\')
     * @param string $baseDir The base directory for the namespace
     * @param bool $prepend Whether to prepend to existing registrations (default: false)
     * @return bool Returns true if registration was successful
     */
    public function addNamespace(string $prefix, string $baseDir, bool $prepend = false): bool
    {
        // Ensure the prefix ends with a namespace separator
        $prefix = trim($prefix, '\\') . '\\';

        // Normalize and validate the base directory
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR . '/') . DIRECTORY_SEPARATOR;

        // Check if directory exists
        if (!is_dir($baseDir)) {
            trigger_error(
                "Base directory '{$baseDir}' does not exist for namespace '{$prefix}'",
                E_USER_NOTICE
            );
            return false;
        }

        // Initialize the namespace prefix array if it doesn't exist
        if (!isset($this->prefixes[$prefix])) {
            $this->prefixes[$prefix] = [];
        }

        // Check if this directory is already registered for this namespace
        if (in_array($baseDir, $this->prefixes[$prefix], true)) {
            return true; // Already registered
        }

        // Add the base directory to the namespace prefix
        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $baseDir);
        } else {
            $this->prefixes[$prefix][] = $baseDir;
        }

        return true;
    }

    /**
     * Register multiple namespace prefixes at once
     * 
     * @param array<string, string|array<string>> $namespaces 
     *        Array of ['Namespace\\' => '/path/to/dir'] or 
     *        ['Namespace\\' => ['/path1', '/path2']]
     * @return bool Returns true if all registrations were successful
     */
    public function addNamespaces(array $namespaces): bool
    {
        $success = true;

        foreach ($namespaces as $prefix => $paths) {
            // Handle both single path and multiple paths
            if (!is_array($paths)) {
                $paths = [$paths];
            }

            foreach ($paths as $path) {
                if (!$this->addNamespace($prefix, $path)) {
                    $success = false;
                }
            }
        }

        return $success;
    }

    /**
     * Get all registered namespace prefixes
     * 
     * @return array<string> Returns array of registered namespace prefixes
     */
    public function getNamespaces(): array
    {
        return array_keys($this->prefixes);
    }

    /**
     * Get base directories for a specific namespace prefix
     * 
     * @param string $prefix The namespace prefix
     * @return array<string> Returns array of base directories for the prefix
     */
    public function getBaseDirs(string $prefix): array
    {
        $prefix = trim($prefix, '\\') . '\\';

        return $this->prefixes[$prefix] ?? [];
    }

    /**
     * Remove a namespace prefix registration
     * 
     * @param string $prefix The namespace prefix to remove
     * @return bool Returns true if namespace was removed
     */
    public function removeNamespace(string $prefix): bool
    {
        $prefix = trim($prefix, '\\') . '\\';

        if (isset($this->prefixes[$prefix])) {
            unset($this->prefixes[$prefix]);
            return true;
        }

        return false;
    }

    /**
     * Clear all namespace registrations
     */
    public function clearNamespaces(): void
    {
        $this->prefixes = [];
    }

    /**
     * The main autoloading method that PHP calls when a class is not found
     * This implements PSR-4 autoloading standard
     * 
     * @param string $class The fully qualified class name to load
     * @return bool Returns true if class was loaded successfully
     */
    public function loadClass(string $class): bool
    {
        // Iterate through all registered namespace prefixes
        foreach ($this->prefixes as $prefix => $baseDirs) {
            // Check if the class uses this namespace prefix
            $len = strlen($prefix);

            // Quick check: class name must start with the prefix
            if (strncmp($prefix, $class, $len) !== 0) {
                continue;
            }

            // Get the relative class name after the prefix
            $relativeClass = substr($class, $len);

            // Try to load the class from each base directory for this prefix
            foreach ($baseDirs as $baseDir) {
                // Convert the relative class name to a file path
                // Replace namespace separators with directory separators
                $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass);

                // Build the full file path
                $file = $baseDir . $filePath . '.php';

                // Try to load the file
                if ($this->loadFile($file)) {
                    return true;
                }
            }
        }

        // Class not found in any registered namespace
        return false;
    }

    /**
     * Load a specific file if it exists and is readable
     * 
     * @param string $file The file path to load
     * @return bool Returns true if file was loaded successfully
     */
    private function loadFile(string $file): bool
    {
        // Normalize the file path to prevent directory traversal
        $file = $this->normalizeFilePath($file);

        // Check if file exists and is readable
        if (file_exists($file) && is_readable($file)) {
            // Load the file
            require $file;

            // Verify that the class was actually defined
            // (This helps catch files that don't define the expected class)
            return true;
        }

        return false;
    }

    /**
     * Normalize file path to prevent directory traversal attacks
     * and resolve relative paths
     * 
     * @param string $file The file path to normalize
     * @return string Returns normalized file path
     */
    private function normalizeFilePath(string $file): string
    {
        // Convert to real path to prevent directory traversal
        // and resolve symlinks
        $realPath = realpath($file);

        // If realpath fails (file doesn't exist yet), return normalized path
        if ($realPath === false) {
            // At least normalize the path by removing redundant separators
            return $this->sanitizePath($file);
        }

        return $realPath;
    }

    /**
     * Sanitize path by removing redundant separators and normalizing
     * 
     * @param string $path The path to sanitize
     * @return string Returns sanitized path
     */
    private function sanitizePath(string $path): string
    {
        // Replace backslashes with forward slashes for consistency
        $path = str_replace('\\', '/', $path);

        // Remove duplicate slashes
        $path = preg_replace('#/+#', '/', $path);

        // Convert back to system-specific separator
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);

        return $path;
    }

    /**
     * Set custom namespace mappings (useful for runtime configuration)
     * 
     * @param array<string, string|array<string>> $mappings 
     *        Array of custom namespace mappings
     */
    public function setCustomMappings(array $mappings): void
    {
        $this->addNamespaces($mappings);
    }

    /**
     * Get the base path of the framework
     * 
     * @return string Returns the framework base path
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Get the path to composer.json file
     * 
     * @return string|null Returns composer.json path or null if not found
     */
    public function getComposerJsonPath(): ?string
    {
        return $this->composerJsonPath;
    }

    /**
     * Get all registered namespace mappings
     * 
     * @return array<string, array<string>> Returns all namespace mappings
     */
    public function getNamespaceMappings(): array
    {
        return $this->prefixes;
    }

    /**
     * Check if a namespace is registered
     * 
     * @param string $prefix The namespace prefix to check
     * @return bool Returns true if namespace is registered
     */
    public function hasNamespace(string $prefix): bool
    {
        $prefix = trim($prefix, '\\') . '\\';
        return isset($this->prefixes[$prefix]);
    }

    /**
     * Get the current autoloader instance (Singleton pattern)
     * This allows for consistent autoloader usage throughout the application
     * 
     * @return self Returns the autoloader instance
     */
    public static function getInstance(): self
    {
        static $instance = null;

        if ($instance === null) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Debug method to show all registered namespaces and their paths
     * Useful for troubleshooting autoloading issues
     * 
     * @return string Returns formatted debug information
     */
    public function debug(): string
    {
        $output = "=== RijanPHP Autoloader Debug Information ===\n\n";
        $output .= "Base Path: {$this->basePath}\n";
        $output .= "Composer.json: " . ($this->composerJsonPath ?? 'Not loaded') . "\n\n";
        $output .= "Registered Namespaces:\n";
        $output .= str_repeat('-', 50) . "\n";

        foreach ($this->prefixes as $prefix => $dirs) {
            $output .= "Namespace: {$prefix}\n";
            foreach ($dirs as $dir) {
                $output .= "  - {$dir}\n";
            }
            $output .= "\n";
        }

        return $output;
    }
}

// Automatically create and register the autoloader when this file is included
// This ensures the autoloader is ready to use immediately
return Autoloader::getInstance();