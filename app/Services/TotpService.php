<?php

namespace App\Services;

use App\Models\TotpEntry;
use OTPHP\TOTP;

class TotpService
{
    /**
     * Generate TOTP code for an entry
     */
    public function generateCode(TotpEntry $entry): string
    {
        $totp = TOTP::create(
            $entry->secret,
            $entry->period ?? 30,
            $entry->algorithm ?? 'sha1',
            $entry->digits ?? 6
        );

        if ($entry->issuer) {
            $totp->setIssuer($entry->issuer);
        }

        $totp->setLabel($entry->name);

        return $totp->now();
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
