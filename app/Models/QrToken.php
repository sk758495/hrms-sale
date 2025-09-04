<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class QrToken extends Model
{
    protected $fillable = [
        'token',
        'user_id',
        'expires_at',
        'used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    public function isValid()
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    public static function generateToken($userId)
    {
        // Clean expired tokens
        self::where('expires_at', '<', now())->delete();
        
        $token = bin2hex(random_bytes(32));
        
        return self::create([
            'token' => $token,
            'user_id' => $userId,
            'expires_at' => now()->addMinutes(5), // 5 minute expiry
        ]);
    }
}