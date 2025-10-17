<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'role',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
     * Check if user is an Admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    /**
     * Check if user is Staff
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }
    /**
     * Check if user is a Customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Bookings made by this user as a customer
     */
    public function customerBookings()
    {
        return $this->hasMany(\App\Models\Booking::class, 'customer_id');
    }

    /**
     * Bookings assigned to this user as staff
     */
    public function staffBookings()
    {
        return $this->hasMany(\App\Models\Booking::class, 'staff_id');
    }

    public function ownedVendors(): HasMany
    {
        return $this->hasMany(Vendor::class, 'owner_id');
    }

    public function vendorCollaborations(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'vendor_collaborators')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function collabSessionsStarted(): HasMany
    {
        return $this->hasMany(CollabSession::class, 'started_by');
    }

    public function collabEventsAuthored(): HasMany
    {
        return $this->hasMany(CollabEvent::class, 'actor_id');
    }

    public function vendorRevisionsAuthored(): HasMany
    {
        return $this->hasMany(VendorRevision::class, 'created_by');
    }
}
