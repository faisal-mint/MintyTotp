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
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value; // Return as-is if decryption fails (for migration purposes)
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
