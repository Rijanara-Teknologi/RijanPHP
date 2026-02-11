<?php

namespace Teguh02\Rijanphp\Core\Security;

class Hash
{
    /**
     * Create a new hash for the given value.
     */
    public static function make(string $value, array $options = []): string
    {
        return password_hash($value, PASSWORD_BCRYPT, $options);
    }

    /**
     * Check the given plain value against a hash.
     */
    public static function check(string $value, string $hashedValue): bool
    {
        return password_verify($value, $hashedValue);
    }

    /**
     * Check if the given hash has been hashed using the given options.
     */
    public static function needsRehash(string $hashedValue, array $options = []): bool
    {
        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, $options);
    }
}
