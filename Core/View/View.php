<?php

namespace Teguh02\Rijanphp\Core\View;

class View
{
    protected static $engine;
    protected static $sections = [];
    protected static $sectionStack = [];
    protected static $layout = null;

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

    /**
     * Render the view. 
     * If the view extends a layout, it will recursively render the layout.
     */
    public static function render($view, $data = [])
    {
        // Reset state for a fresh top-level render if needed
        // Note: sections are NOT reset for nested includes, but we want a clean start for new responses.
        // Actually, since this is a persistent process in some envs, we should be careful.

        $content = self::getEngine()->make($view, $data);

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
