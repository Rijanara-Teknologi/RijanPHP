<?php

namespace Teguh02\Rijanphp\Tests\Feature;

use Teguh02\Rijanphp\Tests\TestCase;

class EncryptionTest extends TestCase
{
    public function test_basic_encryption_and_decryption()
    {
        $value = 'Hello RijanPHP Encryption';
        $encrypted = encrypt($value);

        $this->assertNotEquals($value, $encrypted);
        $this->assertEquals($value, decrypt($encrypted));
    }

    public function test_encryption_with_arrays()
    {
        $value = ['name' => 'Teguh', 'role' => 'Developer'];
        $encrypted = encrypt($value);

        $this->assertIsArray(decrypt($encrypted));
        $this->assertEquals($value, decrypt($encrypted));
    }

    public function test_encryption_is_secure_and_uses_iv()
    {
        $value = 'secret';
        $encrypted1 = encrypt($value);
        $encrypted2 = encrypt($value);

        // Even with same value, encrypted strings should be different due to random IV
        $this->assertNotEquals($encrypted1, $encrypted2);

        $this->assertEquals($value, decrypt($encrypted1));
        $this->assertEquals($value, decrypt($encrypted2));
    }

    public function test_decryption_fails_with_invalid_payload()
    {
        $this->expectException(\RuntimeException::class);
        decrypt('invalid-payload');
    }

    public function test_decryption_fails_with_tampered_payload()
    {
        $encrypted = encrypt('original');
        $payload = json_decode(base64_decode($encrypted), true);

        // Tamper with the value
        $payload['value'] = base64_encode('tampered');
        $tampered = base64_encode(json_encode($payload));

        $this->expectException(\RuntimeException::class);
        decrypt($tampered);
    }
}
