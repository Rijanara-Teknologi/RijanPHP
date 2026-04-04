<?php

namespace Teguh02\Rijanphp\Core\Session;

class Session
{
    protected static $started = false;
    protected static $regenerated = false;

    public static function start()
    {
        if (self::$started) {
            return;
        }

        if (PHP_SAPI === 'cli') {
            if (!isset($_SESSION)) {
                $_SESSION = [];
            }
            self::$started = true;
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            $options = [
                'cookie_lifetime' => 0,
                'cookie_httponly' => true,
                'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'cookie_samesite' => 'Lax',
                'use_strict_mode' => true,
                'use_only_cookies' => 1,
            ];

            session_start($options);
        }

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
        self::$regenerated = false;
    }

    public static function all()
    {
        self::ensureStarted();
        if (isset(\Teguh02\Rijanphp\Core\Rijan::$instance)) {
            return \Teguh02\Rijanphp\Core\Rijan::$instance->session;
        }
        return $_SESSION;
    }

    public static function regenerate($deleteOldSession = true)
    {
        self::ensureStarted();

        if (PHP_SAPI === 'cli') {
            return true;
        }

        if (self::$regenerated) {
            return true;
        }

        $result = session_regenerate_id($deleteOldSession);
        self::$regenerated = true;
        return $result;
    }

    public static function regenerateToken()
    {
        return self::regenerate(true);
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

    public static function pull($key, $default = null)
    {
        $value = self::get($key, $default);
        self::remove($key);
        return $value;
    }

    public static function push($key, $value)
    {
        self::ensureStarted();
        if (!isset($_SESSION[$key]) || !is_array($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        $_SESSION[$key][] = $value;
    }

    public static function increment($key, $amount = 1)
    {
        self::ensureStarted();
        $value = (int) ($_SESSION[$key] ?? 0);
        $_SESSION[$key] = $value + $amount;
        return $_SESSION[$key];
    }

    public static function decrement($key, $amount = 1)
    {
        return self::increment($key, -$amount);
    }

    protected static function ageFlashData()
    {
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
