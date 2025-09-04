<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreakSession extends Model
{
    protected $table = 'breaks';
    
    protected $fillable = [
        'user_id', 
        'work_session_id', 
        'type', 
        'pauses_timer', 
        'start_time', 
        'end_time', 
        'duration_seconds'
    ];
    
    protected $casts = [
        'start_time' => 'datetime', 
        'end_time' => 'datetime', 
        'pauses_timer' => 'boolean',
        'duration_seconds' => 'integer'
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function workSession(): BelongsTo
    {
        return $this->belongsTo(WorkSession::class, 'work_session_id');
    }
}