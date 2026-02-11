<?php

namespace Teguh02\Rijanphp\Tests\Feature;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Core\Security\Hash;

class HashTest extends TestCase
{
    public function test_hash_make_creates_correct_bcrypt_hash()
    {
        $password = 'secret-password';
        $hash = Hash::make($password);

        $this->assertNotEquals($password, $hash);
        $this->assertTrue(password_get_info($hash)['algoName'] === 'bcrypt');
    }

    public function test_hash_check_verifies_correct_password()
    {
        $password = 'secret-password';
        $hash = Hash::make($password);

        $this->assertTrue(Hash::check($password, $hash));
        $this->assertFalse(Hash::check('wrong-password', $hash));
    }

    public function test_hash_helpers_work_correctly()
    {
        $password = 'helper-password';
        $hash = bcrypt($password);

        $this->assertTrue(hash_check($password, $hash));
    }

    public function test_hash_needs_rehash()
    {
        $password = 'rehash-test';
        $hash = Hash::make($password, ['cost' => 5]);

        $this->assertTrue(Hash::needsRehash($hash, ['cost' => 10]));
        $this->assertFalse(Hash::needsRehash($hash, ['cost' => 5]));
    }
}
