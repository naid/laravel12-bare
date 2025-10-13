<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: User has many Personnel records
     */
    public function personnel()
    {
        return $this->hasMany(Personnel::class);
    }

    /**
     * Relationship: User can access many Clients (many-to-many)
     */
    public function clients()
    {
        return $this->belongsToMany(Client::class)
            ->withPivot('access_level')
            ->withTimestamps();
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user can access a specific client
     */
    public function canAccessClient(Client $client): bool
    {
        // Admins can access all clients
        if ($this->isAdmin()) {
            return true;
        }

        // Check if user has access to this specific client
        return $this->clients()->where('client_id', $client->id)->exists();
    }

    /**
     * Get all clients this user can access
     */
    public function accessibleClients()
    {
        // Admins can access all clients
        if ($this->isAdmin()) {
            return Client::query();
        }

        // Return only assigned clients
        return $this->clients();
    }
}
