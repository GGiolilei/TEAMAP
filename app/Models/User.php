<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Profile;
use App\Models\task;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'email', 
        'password',
        'role'
    ];

    /**
     * Get the profile configuration details for this user.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * The focus attribute interest tags chosen by this user.
     */
    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Interest::class, 'user_interest');
    }

    /**
     * Get the project workspaces created/owned by this user.
     */
    public function ownedLobbies(): HasMany
    {
        return $this->hasMany(Lobby::class, 'owner_id');
    }

    /**
     * Relationship Alias for backward compatibility:
     * Allows method-style chaining like $user->hostedLobbies()->withCount(...)->get()
     */
    public function hostedLobbies(): HasMany
    {
        return $this->ownedLobbies();
    }

    /**
     * The project workspace lobbies this user has joined as a team participant.
     */
    public function lobbies(): BelongsToMany
    {
        return $this->belongsToMany(Lobby::class, 'lobby_members', 'user_id', 'lobby_id')
                    ->using(LobbyMember::class)
                    ->withPivot('status')
                    ->withTimestamps();
    }

    /**
     * Dynamic Attribute Model Alias for backward compatibility:
     * Maps cleanly back to the corrected Eloquent relationship collection.
     */
    public function getJoinedLobbiesAttribute()
    {
        return $this->lobbies;
    }

    /**
     * Dynamic Attribute Model Alias for backward compatibility:
     * Maps $user->hostedLobbies cleanly to the underlying collection.
     */
    public function getHostedLobbiesAttribute()
    {
        return $this->ownedLobbies;
    }
    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(Task::class);
}
/////////baru nih broski//////

public function isAdmin(): bool
{
    return $this->role === 'admin';
}
public function filedReports(): HasMany
{
    return $this->hasMany(UserReport::class, 'reporter_id');
}
public function reportsAgainst(): HasMany
{
    return $this->hasMany(UserReport::class, 'reported_user_id');
}
// Add this static boot method inside app/Models/User.php
protected static function boot()
{
    parent::boot();

    static::deleting(function ($user) {
        // Clear all reports filed by or against this user
        \App\Models\UserReport::where('reporter_id', $user->id)
            ->orWhere('reported_user_id', $user->id)
            ->delete();

        // Detach user from all joined lobby member pivot tables
        $user->lobbies()->detach();
    });
}
}