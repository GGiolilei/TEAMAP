<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'title',
        'status',
        'user_id',
        'lobby_id',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function lobby(): BelongsTo
    {
        return $this->belongsTo(Lobby::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}