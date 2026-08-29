<?php

declare(strict_types=1);

namespace App\Passport;

use Defuse\Crypto\Crypto;
use Illuminate\Contracts\Encryption\Encrypter;
use Laravel\Passport\Passport;
use Throwable;

/**
 * Decrypts League OAuth2 auth-code / refresh-token request payloads with the
 * same key Passport hands to the AuthorizationServer, so the workspace bound at
 * consent time can be read back when the code is exchanged.
 */
class OAuthPayloadDecryptor
{
    public function __construct(private Encrypter $encrypter) {}

    /**
     * @return array<string, mixed>|null
     */
    public function decrypt(string $encrypted): ?array
    {
        try {
            $json = Crypto::decryptWithPassword(
                $encrypted,
                Passport::tokenEncryptionKey($this->encrypter),
            );
        } catch (Throwable) {
            return null;
        }

        $payload = json_decode($json, true);

        return is_array($payload) ? $payload : null;
    }

    /**
     * Encrypt a payload the way League's CryptTrait does (used by tests).
     *
     * @param  array<string, mixed>  $payload
     */
    public function encrypt(array $payload): string
    {
        return Crypto::encryptWithPassword(
            json_encode($payload, JSON_THROW_ON_ERROR),
            Passport::tokenEncryptionKey($this->encrypter),
        );
    }
}
