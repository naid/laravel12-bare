<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{


    protected $fillable = [
        'name',
        'industry',
        'services_provided',
        'ccn',
        'npi',
        'address',
        'city',
        'state',
        'state_code',
        'postal_code',
        'country',
        'contact_email',
        'contact_phone',
        'contact_number',
        'website_url',
        'created_on',
        'created_by',
        'updated_on',
        'updated_by',
        'active',
    ];

    protected $casts = [
        'created_on' => 'datetime',
        'updated_on' => 'datetime',
    ];

    /**
     * Relationship: Client has many Personnel
     */
    public function personnel()
    {
        return $this->hasMany(Personnel::class);
    }

    /**
     * Relationship: Client can be accessed by many Users (many-to-many)
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('access_level')
            ->withTimestamps();
    }
}
