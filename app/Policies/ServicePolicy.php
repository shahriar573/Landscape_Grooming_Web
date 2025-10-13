<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Service;
class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view services
    }
    public function view(User $user, Service $service): bool
    {
        return true; // Anyone can view a service
    }
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }
    public function update(User $user, Service $service): bool
    {
        return $user->isAdmin();
    }
    public function delete(User $user, Service $service): bool
    {
        return $user->isAdmin();
    }
}
