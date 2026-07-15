<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'password'
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
}