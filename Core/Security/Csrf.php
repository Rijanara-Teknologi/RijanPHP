<?php

namespace Teguh02\Rijanphp\Core\Security;

use Teguh02\Rijanphp\Core\Session\Session;

class Csrf
{
    /**
     * Generate a new CSRF token and store it in the session.
     *
     * @return string
     */
    public static function generate()
    {
        $token = bin2hex(random_bytes(32));
        Session::set('_token', $token);
        return $token;
    }

    /**
     * Get the current CSRF token from the session.
     * Generates a new one if it doesn't exist.
     *
     * @return string
     */
    public static function token()
    {
        if (Session::has('_token')) {
            return Session::get('_token');
        }

        return self::generate();
    }

    /**
     * Verify the given token against the session token.
     *
     * @param string $token
     * @return bool
     */
    public static function verify($token)
    {
        $sessionToken = Session::get('_token');

        if (!$sessionToken || !$token) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }
}
