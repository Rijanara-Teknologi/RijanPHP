<?php

namespace Teguh02\Rijanphp\Core\Cookie;

class CookieManager
{
    protected $key;
    protected $cipher;

    public function __construct()
    {
        $config = config('app');
        $this->key = base64_decode(substr($config['key'], 7)); // Remove 'base64:' prefix
        $this->cipher = $config['cipher'];
    }

    public function encrypt($value)
    {
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
        $value = \openssl_encrypt(serialize($value), $this->cipher, $this->key, 0, $iv);

        if ($value === false) {
            throw new \Exception('Could not encrypt the data.');
        }

        $mac = hash_hmac('sha256', $iv . $value, $this->key);

        return base64_encode(json_encode(compact('iv', 'value', 'mac')));
    }

    public function decrypt($payload)
    {
        $payload = json_decode(base64_decode($payload), true);

        if (!$this->validPayload($payload)) {
            throw new \Exception('The payload is invalid.');
        }

        if (!$this->validMac($payload)) {
            throw new \Exception('The MAC is invalid.');
        }

        $iv = $payload['iv'];
        $decrypted = \openssl_decrypt($payload['value'], $this->cipher, $this->key, 0, $iv);

        if ($decrypted === false) {
            throw new \Exception('Could not decrypt the data.');
        }

        return unserialize($decrypted);
    }

    protected function validPayload($payload)
    {
        return is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']) &&
            strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length($this->cipher);
    }

    protected function validMac(array $payload)
    {
        $calculated = hash_hmac('sha256', $payload['iv'] . $payload['value'], $this->key);

        return hash_equals($calculated, $payload['mac']);
    }
}
