<?php

namespace Teguh02\Rijanphp\Core\View;

class ViewEngine
{
    protected $paths = [];

    public function __construct()
    {
        // No centralized config
    }

    public function addPath($path)
    {
        $this->paths[] = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
    }

    /**
     * Evaluate the view file and return the content.
     */
    public function make($view, $data = [])
    {
        $viewFile = $this->findView($view);

        return $this->evaluate($viewFile, $data);
    }

    /**
     * Find the view file path.
     */
    protected function findView($view)
    {
        $name = str_replace('.', DIRECTORY_SEPARATOR, $view);

        foreach (array_reverse($this->paths) as $path) {
            $fullPath = $path . $name . '.php';
            if (file_exists($fullPath)) {
                return $fullPath;
            }
        }

        throw new \Exception("View [{$view}] not found.");
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
