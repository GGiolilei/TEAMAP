<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UserReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'reporter_id',
        'reported_user_id',
        'reason',
        'status',
    ];

    /**
     * Auto-generate string report_id (e.g. REP-89A1B2C3) on creation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($report) {
            if (empty($report->report_id)) {
                $report->report_id = 'REP-' . strtoupper(Str::random(8));
            }
        });
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }
}