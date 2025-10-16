<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'website',
        'address',
        'description',
        'is_active',
        'metadata',
        'owner_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function collaborators(): HasMany
    {
        return $this->hasMany(VendorCollaborator::class);
    }

    public function collaboratorUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'vendor_collaborators')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CollabSession::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(CollabEvent::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(VendorRevision::class);
    }
}
