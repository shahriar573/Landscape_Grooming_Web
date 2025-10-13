<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Booking;
class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Users can view bookings (filtered in controller)
    }
    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || 
               $booking->customer_id === $user->id || 
               $booking->staff_id === $user->id;
    }
    public function create(User $user): bool
    {
        return $user->isCustomer() || $user->isAdmin();
    }
    public function update(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $booking->customer_id === $user->id;
    }
    public function delete(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $booking->customer_id === $user->id;
    }
    public function assignStaff(User $user, Booking $booking): bool
    {
        return $user->isAdmin();
    }
}
