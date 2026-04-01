<?php

namespace Teguh02\Rijanphp\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Security\Encrypter;

class EncrypterTest extends TestCase
{
    protected $encrypter;
    protected $key;

    protected function setUp(): void
    {
        parent::setUp();
        $this->key = random_bytes(32);
        $this->encrypter = new Encrypter($this->key);
    }

    public function testEncryptReturnsBase64String()
    {
        $encrypted = $this->encrypter->encrypt('test data');
        $this->assertIsString($encrypted);
    }

    public function testEncryptDecryptRoundTrip()
    {
        $data = 'sensitive information';
        $encrypted = $this->encrypter->encrypt($data);
        $decrypted = $this->encrypter->decrypt($encrypted);

        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptWithArray()
    {
        $data = ['name' => 'John', 'email' => 'john@example.com'];
        $encrypted = $this->encrypter->encrypt($data);
        $decrypted = $this->encrypter->decrypt($encrypted);

        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptWithObject()
    {
        $data = new \stdClass();
        $data->id = 1;
        $data->name = 'Test';

        $encrypted = $this->encrypter->encrypt($data);
        $decrypted = $this->encrypter->decrypt($encrypted);

        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptWithNull()
    {
        $encrypted = $this->encrypter->encrypt(null);
        $decrypted = $this->encrypter->decrypt($encrypted);

        $this->assertNull($decrypted);
    }

    public function testEncryptWithEmptyString()
    {
        $encrypted = $this->encrypter->encrypt('');
        $decrypted = $this->encrypter->decrypt($encrypted);

        $this->assertEquals('', $decrypted);
    }

    public function testEncryptWithNumbers()
    {
        $encrypted = $this->encrypter->encrypt(12345);
        $decrypted = $this->encrypter->decrypt($encrypted);

        $this->assertEquals(12345, $decrypted);
    }

    public function testEncryptWithBoolean()
    {
        $encrypted = $this->encrypter->encrypt(true);
        $decrypted = $this->encrypter->decrypt($encrypted);

        $this->assertTrue($decrypted);
    }

    public function testDifferentEncryptionsProduceDifferentResults()
    {
        $encrypted1 = $this->encrypter->encrypt('same data');
        $encrypted2 = $this->encrypter->encrypt('same data');

        $this->assertNotEquals($encrypted1, $encrypted2);
    }

    public function testDecryptWithWrongKeyThrowsException()
    {
        $wrongKey = random_bytes(32);
        $wrongEncrypter = new Encrypter($wrongKey);

        $encrypted = $this->encrypter->encrypt('secret');

        $this->expectException(\Exception::class);
        $wrongEncrypter->decrypt($encrypted);
    }

    public function testDecryptWithTamperedDataThrowsException()
    {
        $encrypted = $this->encrypter->encrypt('secret');
        $tampered = $encrypted . 'tampered';

        $this->expectException(\Exception::class);
        $this->encrypter->decrypt($tampered);
    }

    public function testDecryptWithInvalidJsonThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->encrypter->decrypt('not-valid-json');
    }

    public function testKeyLengthValidation()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Encrypter('short-key');
    }

    public function testAes128KeyLength()
    {
        $key16 = random_bytes(16);
        $encrypter = new Encrypter($key16);

        $encrypted = $encrypter->encrypt('test');
        $decrypted = $encrypter->decrypt($encrypted);

        $this->assertEquals('test', $decrypted);
    }
}
