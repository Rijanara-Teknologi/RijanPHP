<?php

namespace Teguh02\Rijanphp\Core\View;

class ViewEngine
{
    protected $paths = [];
    protected $namespaces = [];

    public function __construct()
    {
        // No centralized config
    }

    public function addPath($path)
    {
        $this->paths[] = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
    }

    public function addNamespace($namespace, $path)
    {
        $this->namespaces[$namespace] = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
    }

    public function make($view, $data = [], $namespace = null)
    {
        $viewFile = $this->findView($view, $namespace);

        return $this->evaluate($viewFile, $data);
    }

    /**
     * Find the view file path.
     */
    protected function findView($view, $namespace = null)
    {
        // If no explicit namespace, try the provided contextual namespace
        if (strpos($view, '::') === false && $namespace) {
            $namespacedView = $namespace . '::' . $view;
            try {
                return $this->findView($namespacedView);
            } catch (\Exception $e) {
                // Fallback to global search
            }
        }

        // Handle explicit namespace
        if (strpos($view, '::') !== false) {
            list($ns, $name) = explode('::', $view);
            $name = str_replace(['.', '/'], DIRECTORY_SEPARATOR, $name);

            if (isset($this->namespaces[$ns])) {
                $fullPath = $this->namespaces[$ns] . $name . '.php';
                $fullPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fullPath);

                if (file_exists($fullPath)) {
                    return $fullPath;
                }
            }

            // Fallback: Try lowercase namespace (for case-sensitive file systems)
            $nsLower = strtolower($ns);
            if (isset($this->namespaces[$nsLower])) {
                $fullPath = $this->namespaces[$nsLower] . $name . '.php';
                $fullPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fullPath);

                if (file_exists($fullPath)) {
                    return $fullPath;
                }
            }
        }

        // Global search
        $name = str_replace(['.', '/'], DIRECTORY_SEPARATOR, $view);

        foreach (array_reverse($this->paths) as $path) {
            $fullPath = $path . $name . '.php';
            $fullPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fullPath);

            if (file_exists($fullPath)) {
                return $fullPath;
            }
        }

        // Check if debug mode or CI
        $debugInfo = "";
        if (getenv('CI') || getenv('GITHUB_ACTIONS') || getenv('APP_DEBUG')) {
            $debugInfo .= "\nSearched Namespaces: " . print_r($this->namespaces, true);
            $debugInfo .= "\nSearched Paths: " . print_r($this->paths, true);
        }

        throw new \Exception("View [{$view}] not found." . $debugInfo);
    }

    /**
     * Extract data and include the file.
     */
    protected function evaluate($__path, $__data)
    {
        extract($__data);
        ob_start();
        include $__path;
        return ob_get_clean();
    }
}
