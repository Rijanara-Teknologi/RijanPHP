<?php

namespace Teguh02\Rijanphp\Core\View;

class View
{
    protected static $engine;
    protected static $sections = [];
    protected static $sectionStack = [];
    protected static $layout = null;
    protected static $currentNamespace = null;

    public static function getEngine()
    {
        if (!self::$engine) {
            self::$engine = new ViewEngine();
        }
        return self::$engine;
    }

    public static function addPath($path)
    {
        self::getEngine()->addPath($path);
    }

    public static function addNamespace($namespace, $path)
    {
        self::getEngine()->addNamespace($namespace, $path);
    }

    public static function setCurrentNamespace($namespace)
    {
        self::$currentNamespace = $namespace;
    }

    /**
     * Render the view. 
     * If the view extends a layout, it will recursively render the layout.
     */
    public static function render($view, $data = [])
    {
        $content = self::getEngine()->make($view, $data, self::$currentNamespace);

        if (self::$layout) {
            $layout = self::$layout;
            self::$layout = null; // Clear it to allow the layout itself to be rendered
            return self::render($layout, $data);
        }

        return $content;
    }

    // --- Layout & Section Helpers ---

    public static function extends($layout)
    {
        self::$layout = $layout;
    }

    public static function section($name)
    {
        self::$sectionStack[] = $name;
        ob_start();
    }

    public static function endSection()
    {
        if (empty(self::$sectionStack)) {
            return;
        }

        $name = array_pop(self::$sectionStack);
        self::$sections[$name] = ob_get_clean();
    }

    public static function yield($name, $default = '')
    {
        return isset(self::$sections[$name]) ? trim(self::$sections[$name]) : $default;
    }
}
