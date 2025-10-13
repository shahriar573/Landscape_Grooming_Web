<?php
namespace App\Providers;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Service;
use App\Models\Booking;
use App\Policies\ServicePolicy;
use App\Policies\BookingPolicy;
class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Service::class => ServicePolicy::class,
        Booking::class => BookingPolicy::class,
    ];
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
