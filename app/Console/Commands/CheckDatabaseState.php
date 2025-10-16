<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Console\Command;

class CheckDatabaseState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:check-state';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check current database state for users, bookings, and services';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== DATABASE STATE CHECK ===');
        
        // Users
        $this->info("\n🔸 USERS:");
        $users = User::all();
        foreach ($users as $user) {
            $this->line("  - {$user->name} ({$user->email}) - Role: {$user->role}");
        }
        
        if ($users->isEmpty()) {
            $this->warn("  No users found! Run seeders or create users.");
        }
        
        // Services
        $this->info("\n🔸 SERVICES:");
        $services = Service::all();
        foreach ($services as $service) {
            $this->line("  - {$service->name} (\${$service->price})");
        }
        
        if ($services->isEmpty()) {
            $this->warn("  No services found! Create some services first.");
        }
        
        // Bookings
        $this->info("\n🔸 BOOKINGS:");
        $bookings = Booking::with(['customer', 'staff', 'service'])->get();
        foreach ($bookings as $booking) {
            $staffName = $booking->staff ? $booking->staff->name : 'UNASSIGNED';
            $this->line("  - Booking #{$booking->id}: {$booking->service->name} for {$booking->customer->name}");
            $this->line("    Staff: {$staffName}, Status: {$booking->status}, Date: {$booking->scheduled_at->format('M j, Y')}");
        }
        
        if ($bookings->isEmpty()) {
            $this->warn("  No bookings found!");
        }
        
        // Summary
        $this->info("\n📊 SUMMARY:");
        $this->table(['Type', 'Count'], [
            ['Users', $users->count()],
            ['- Admins', $users->where('role', 'admin')->count()],
            ['- Staff', $users->where('role', 'staff')->count()],
            ['- Customers', $users->where('role', 'customer')->count()],
            ['Services', $services->count()],
            ['Bookings', $bookings->count()],
            ['- Unassigned', $bookings->whereNull('staff_id')->count()],
        ]);
        
        $this->info("\n✅ Check complete!");
        
        return 0;
    }
}
