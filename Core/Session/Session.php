<?php

namespace Teguh02\Rijanphp\Core\Session;

class Session
{
    protected static $started = false;

    public static function start()
    {
        if (self::$started) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Sync Rijan session with PHP session
        if (isset(\Teguh02\Rijanphp\Core\Rijan::$instance)) {
            \Teguh02\Rijanphp\Core\Rijan::$instance->session = &$_SESSION;
        }

        self::$started = true;
        self::ageFlashData();
    }

    public static function set($key, $value)
    {
        self::ensureStarted();
        $_SESSION[$key] = $value;
        if (isset(\Teguh02\Rijanphp\Core\Rijan::$instance)) {
            \Teguh02\Rijanphp\Core\Rijan::$instance->session[$key] = $value;
        }
    }

    public static function get($key, $default = null)
    {
        self::ensureStarted();
        if (isset(\Teguh02\Rijanphp\Core\Rijan::$instance)) {
            return \Teguh02\Rijanphp\Core\Rijan::$instance->session[$key] ?? $default;
        }
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key)
    {
        self::ensureStarted();
        if (isset(\Teguh02\Rijanphp\Core\Rijan::$instance)) {
            return isset(\Teguh02\Rijanphp\Core\Rijan::$instance->session[$key]);
        }
        return isset($_SESSION[$key]);
    }

    public static function remove($key)
    {
        self::ensureStarted();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
        if (isset(\Teguh02\Rijanphp\Core\Rijan::$instance) && isset(\Teguh02\Rijanphp\Core\Rijan::$instance->session[$key])) {
            unset(\Teguh02\Rijanphp\Core\Rijan::$instance->session[$key]);
        }
    }

    public static function clear()
    {
        self::ensureStarted();
        session_unset();
        if (isset(\Teguh02\Rijanphp\Core\Rijan::$instance)) {
            \Teguh02\Rijanphp\Core\Rijan::$instance->session = [];
        }
    }

    public static function destroy()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        if (isset(\Teguh02\Rijanphp\Core\Rijan::$instance)) {
            \Teguh02\Rijanphp\Core\Rijan::$instance->session = [];
        }
        self::$started = false;
    }

    public static function all()
    {
        self::ensureStarted();
        if (isset(\Teguh02\Rijanphp\Core\Rijan::$instance)) {
            return \Teguh02\Rijanphp\Core\Rijan::$instance->session;
        }
        return $_SESSION;
    }

    public static function regenerate()
    {
        self::ensureStarted();
        return session_regenerate_id(true);
    }

    public static function flash($key, $value)
    {
        self::ensureStarted();
        $_SESSION['flash_data_new'][] = $key;
        self::set($key, $value);
    }

    public static function getFlash($key, $default = null)
    {
        if (self::has($key)) {
            // Check if it's flash data, if so, mark as old immediately or handle via ageFlashData lifecycle
            // For simplicity, we just return it. The cleanup happens in ageFlashData next request.
            return self::get($key, $default);
        }
        return $default;
    }

    public static function keepFlash($key)
    {
        self::ensureStarted();
        if (isset($_SESSION['flash_data_old'])) {
            $index = array_search($key, $_SESSION['flash_data_old']);
            if ($index !== false) {
                unset($_SESSION['flash_data_old'][$index]);
                $_SESSION['flash_data_new'][] = $key;
            }
        }
    }

    protected static function ageFlashData()
    {
        // Cycles flash data: new -> old -> deleted
        if (isset($_SESSION['flash_data_old'])) {
            foreach ($_SESSION['flash_data_old'] as $key) {
                self::remove($key);
            }
        }

        $_SESSION['flash_data_old'] = $_SESSION['flash_data_new'] ?? [];
        $_SESSION['flash_data_new'] = [];
    }

    protected static function ensureStarted()
    {
        if (!self::$started) {
            self::start();
        }
    }
}
