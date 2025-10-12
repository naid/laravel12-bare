<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    protected $table = 'personnel';

    // Custom timestamp column names to match database
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    protected $fillable = [
        'client_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'position',
        'department',
        'hire_date',
        'created_by',
        'updated_by',
        'active',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'created_on' => 'datetime',
        'updated_on' => 'datetime',
    ];

    /**
     * Relationship: Personnel belongs to a Client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relationship: Personnel belongs to a User (optional)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
