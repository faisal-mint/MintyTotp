<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class TotpEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'secret',
        'issuer',
        'algorithm',
        'digits',
        'period',
    ];

    protected $casts = [
        'digits' => 'integer',
        'period' => 'integer',
    ];

    protected $attributes = [
        'algorithm' => 'sha1',
        'digits' => 6,
        'period' => 30,
    ];

    /**
     * Encrypt the secret before saving
     */
    public function setSecretAttribute($value)
    {
        $this->attributes['secret'] = Crypt::encryptString($value);
    }

    /**
     * Decrypt the secret when retrieving
     */
    public function getSecretAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        try {
            $decrypted = Crypt::decryptString($value);
            // Verify it's not still encrypted (encrypted strings typically start with 'eyJ' for base64 JSON)
            if (str_starts_with($decrypted, 'eyJ') && strlen($decrypted) > 50) {
                // This looks like it might still be encrypted, try again
                \Log::warning('Secret might still be encrypted for entry ' . ($this->id ?? 'new'));
            }
            return $decrypted;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            \Log::error('Failed to decrypt secret for entry ' . ($this->id ?? 'new') . ': ' . $e->getMessage());
            // Return as-is if decryption fails - this will cause an error in TOTP generation
            // which we'll handle in the controller
            return $value;
        } catch (\Exception $e) {
            \Log::error('Unexpected error decrypting secret for entry ' . ($this->id ?? 'new') . ': ' . $e->getMessage());
            return $value;
        }
    }

    /**
     * Get the display name (issuer + name or just name)
     */
    public function getDisplayNameAttribute()
    {
        if ($this->issuer) {
            return "{$this->issuer} ({$this->name})";
        }
        return $this->name;
    }
}
