<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkSession extends Model
{
    protected $table = 'work_sessions';
    
    protected $fillable = [
        'user_id', 
        'start_time', 
        'end_time', 
        'duration_seconds', 
        'end_reason'
    ];
    
    protected $casts = [
        'start_time' => 'datetime', 
        'end_time' => 'datetime',
        'duration_seconds' => 'integer'
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function breaks(): HasMany
    {
        return $this->hasMany(BreakSession::class, 'work_session_id');
    }
}