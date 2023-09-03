<?php

namespace App\Service;

class Encrypter
{
    private string $key;
    private string $cipher;

    public function __construct(string $key, string $cipher = 'aes-256-cbc')
    {
        if (!in_array($cipher, openssl_get_cipher_methods(), true)) {
            throw new \InvalidArgumentException(sprintf('Unsupported cipher "%s"', $cipher));
        }

        if ('' === $key) {
            throw new \InvalidArgumentException('Key cannot be empty');
        }

        $this->key = $key;
        $this->cipher = $cipher;
    }

    public function encryptString(string $string): string
    {
        $iv = $this->getIv();

        return sprintf(
            '%s:%s',
            openssl_encrypt(
                $string,
                $this->cipher,
                $this->key,
                0,
                $iv
            ),
            base64_encode($iv)
        );
    }

    public function decryptString(string $string): string
    {
        $chunks = explode (':', $string);

        if (2 !== count($chunks)) {
            throw new \InvalidArgumentException('Malformed encrypted string');
        }

        return openssl_decrypt(
            $chunks[0],
            $this->cipher,
            $this->key,
            0,
            base64_decode($chunks[1])
        );
    }

    private function getIv(): string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));

        if (false === $iv) {
            throw new \RuntimeException('unable to generate random bytes');
        }

        return $iv;
    }
}
