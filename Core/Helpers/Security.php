<?php

use Teguh02\Rijanphp\Core\Security\Hash;

if (!function_exists('hash_make')) {
    /**
     * Create a new hash for the given value.
     */
    function hash_make(string $value, array $options = []): string
    {
        return Hash::make($value, $options);
    }
}

if (!function_exists('hash_check')) {
    /**
     * Check the given plain value against a hash.
     */
    function hash_check(string $value, string $hashedValue): bool
    {
        return Hash::check($value, $hashedValue);
    }
}

if (!function_exists('bcrypt')) {
    /**
     * Hash the given value using bcrypt.
     */
    function bcrypt(string $value, array $options = []): string
    {
        return Hash::make($value, $options);
    }
}

if (!function_exists('encrypt')) {
    /**
     * Encrypt the given value.
     */
    function encrypt($value, bool $serialize = true)
    {
        return crypt_service()->encrypt($value, $serialize);
    }
}

if (!function_exists('decrypt')) {
    /**
     * Decrypt the given value.
     */
    function decrypt(string $value, bool $unserialize = true)
    {
        return crypt_service()->decrypt($value, $unserialize);
    }
}

if (!function_exists('crypt_service')) {
    /**
     * Get the encrypter instance.
     */
    function crypt_service(): \Teguh02\Rijanphp\Core\Security\Encrypter
    {
        static $instance;

        if (!$instance) {
            $config = config('app');
            $key = $config['key'] ?? '';

            if (strpos($key, 'base64:') === 0) {
                $key = base64_decode(substr($key, 7));
            }

            $instance = new \Teguh02\Rijanphp\Core\Security\Encrypter(
                $key,
                $config['cipher'] ?? 'AES-256-CBC'
            );
        }

        return $instance;
    }
}
