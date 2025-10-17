<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user = null): bool
    {
        return true;
    }

    public function view(?User $user, Vendor $vendor): bool
    {
        if ($vendor->is_active) {
            return true;
        }

        if (!$user) {
            return false;
        }

        return $this->collaborate($user, $vendor);
    }

    public function collaborate(User $user, Vendor $vendor): bool
    {
        if ($vendor->owner_id === $user->id) {
            return true;
        }

        return $vendor->collaborators()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function manageCollaborators(User $user, Vendor $vendor): bool
    {
        if ($vendor->owner_id === $user->id) {
            return true;
        }

        return $vendor->collaborators()
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'manager'])
            ->exists();
    }
}
