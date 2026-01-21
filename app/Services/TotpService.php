<?php

namespace App\Services;

use App\Models\TotpEntry;
use Illuminate\Support\Facades\Log;
use OTPHP\TOTP;

class TotpService
{
    /**
     * Generate TOTP code for an entry
     */
    public function generateCode(TotpEntry $entry): string
    {
        $secret = $entry->secret;
        
        // Validate that secret is not empty and appears to be base32
        if (empty($secret)) {
            throw new \InvalidArgumentException('TOTP secret is empty');
        }

        // Check if secret looks like it might still be encrypted (Laravel encrypted strings are base64 JSON)
        if (str_starts_with($secret, 'eyJ') && strlen($secret) > 50) {
            throw new \InvalidArgumentException('TOTP secret appears to be encrypted. Decryption may have failed.');
        }

        try {
            $totp = TOTP::create(
                $secret,
                $entry->period ?? 30,
                $entry->algorithm ?? 'sha1',
                $entry->digits ?? 6
            );

            if ($entry->issuer) {
                $totp->setIssuer($entry->issuer);
            }

            $totp->setLabel($entry->name);

            return $totp->now();
        } catch (\OTPHP\Exception\SecretDecodingException $e) {
            Log::error('Base32 decoding failed for entry ' . $entry->id . '. Secret length: ' . strlen($secret));
            throw new \InvalidArgumentException('Invalid TOTP secret format. Secret may not be properly decrypted or is not base32 encoded.');
        }
    }

    /**
     * Get remaining seconds until code refresh
     */
    public function getRemainingSeconds(TotpEntry $entry): int
    {
        $period = $entry->period;
        $currentTime = time();
        $remaining = $period - ($currentTime % $period);
        return $remaining;
    }

    /**
     * Verify a TOTP code
     */
    public function verifyCode(TotpEntry $entry, string $code): bool
    {
        $totp = TOTP::create(
            $entry->secret,
            $entry->period ?? 30,
            $entry->algorithm ?? 'sha1',
            $entry->digits ?? 6
        );

        return $totp->verify($code);
    }

    /**
     * Parse TOTP URI (otpauth://totp/...)
     */
    public function parseUri(string $uri): array
    {
        $parsed = parse_url($uri);
        
        if (!isset($parsed['scheme']) || $parsed['scheme'] !== 'otpauth') {
            throw new \InvalidArgumentException('Invalid TOTP URI scheme');
        }

        if (!isset($parsed['host']) || $parsed['host'] !== 'totp') {
            throw new \InvalidArgumentException('Invalid TOTP URI host');
        }

        $path = trim($parsed['path'], '/');
        $parts = explode(':', $path, 2);
        
        $issuer = $parts[0] ?? null;
        $name = $parts[1] ?? $parts[0] ?? 'Unknown';

        parse_str($parsed['query'] ?? '', $query);

        return [
            'name' => urldecode($name),
            'issuer' => isset($query['issuer']) ? urldecode($query['issuer']) : ($issuer ?: null),
            'secret' => $query['secret'] ?? '',
            'algorithm' => strtolower($query['algorithm'] ?? 'sha1'),
            'digits' => (int) ($query['digits'] ?? 6),
            'period' => (int) ($query['period'] ?? 30),
        ];
    }
}
