<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    use HasFactory;
    
    protected $fillable = ['draw_session_id', 'participant_id', 'valid'];

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function session()
    {
        return $this->belongsTo(DrawSession::class, 'draw_session_id');
    }

    public function scopeValid($query)
    {
        return $query->where('valid', true);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('draw_session_id', $sessionId);
    }
}