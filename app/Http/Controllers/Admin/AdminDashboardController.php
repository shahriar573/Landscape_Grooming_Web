<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function dashboard()
    {
        // Core metrics
        $totalUsers = User::count();
        $totalServices = Service::count();
        $totalBookings = Booking::count();
        $totalRevenue = Booking::sum('price');
        
        // Recent activity
        $recentBookings = Booking::with(['service', 'customer', 'staff'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Monthly revenue chart data
        $monthlyRevenue = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(price) as revenue'),
            DB::raw('COUNT(*) as bookings')
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        // Service popularity
        $serviceStats = Service::withCount('bookings')
            ->with(['bookings' => function($query) {
                $query->select('service_id', DB::raw('SUM(price) as total_revenue'))
                      ->groupBy('service_id');
            }])
            ->get()
            ->map(function($service) {
                return [
                    'name' => $service->name,
                    'bookings_count' => $service->bookings_count,
                    'total_revenue' => $service->bookings->sum('price') ?? 0
                ];
            });

        // Staff performance
        $staffStats = User::where('role', 'staff')
            ->withCount(['staffBookings as completed_bookings' => function($query) {
                $query->where('status', 'completed');
            }])
            ->with(['staffBookings' => function($query) {
                $query->select('staff_id', DB::raw('SUM(price) as total_revenue'))
                      ->where('status', 'completed')
                      ->groupBy('staff_id');
            }])
            ->get()
            ->map(function($staff) {
                return [
                    'name' => $staff->name,
                    'completed_bookings' => $staff->completed_bookings,
                    'total_revenue' => $staff->staffBookings->sum('price') ?? 0
                ];
            });

        // Booking status distribution
        $bookingStatusStats = Booking::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return view('admin.dashboard', compact(
            'totalUsers', 'totalServices', 'totalBookings', 'totalRevenue',
            'recentBookings', 'monthlyRevenue', 'serviceStats', 'staffStats',
            'bookingStatusStats'
        ));
    }

    public function services()
    {
        $services = Service::withCount('bookings')
            ->with(['bookings' => function($query) {
                $query->select('service_id', DB::raw('SUM(price) as total_revenue'))
                      ->groupBy('service_id');
            }])
            ->paginate(15);

        // Metrics used by the view; keep them schema-safe (no dependency on optional columns)
        $totalServices = Service::count();
        // If there is no is_active column, treat all as active for display purposes
        $activeServices = $totalServices;
        $totalBookings = Booking::count();
        $avgServicePrice = (float) (Service::avg('price') ?? 0);

        return view('admin.services.index', compact(
            'services', 'totalServices', 'activeServices', 'totalBookings', 'avgServicePrice'
        ));
    }

    public function billing()
    {
        // Revenue by month
        $monthlyBilling = Booking::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(price) as revenue'),
            DB::raw('COUNT(*) as bookings'),
            DB::raw('AVG(price) as avg_booking_value')
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->get();

        // Top customers by revenue
        $topCustomers = User::where('role', 'customer')
            ->join('bookings', 'users.id', '=', 'bookings.customer_id')
            ->select('users.name', 'users.email', 
                DB::raw('SUM(bookings.price) as total_spent'),
                DB::raw('COUNT(bookings.id) as total_bookings')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        // Recent transactions
        $recentTransactions = Booking::with(['service', 'customer'])
            ->select('*')
            ->latest()
            ->limit(20)
            ->get();

        // Payment status summary
        $paymentStats = [
            'total_pending' => Booking::where('status', 'pending')->sum('price'),
            'total_confirmed' => Booking::where('status', 'confirmed')->sum('price'),
            'total_completed' => Booking::where('status', 'completed')->sum('price'),
            'total_cancelled' => Booking::where('status', 'cancelled')->sum('price')
        ];

        return view('admin.billing', compact(
            'monthlyBilling', 'topCustomers', 'recentTransactions', 'paymentStats'
        ));
    }

    public function users()
    {
        $users = User::withCount(['customerBookings', 'staffBookings'])
            ->when(request('role'), function($query, $role) {
                return $query->where('role', $role);
            })
            ->when(request('search'), function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate(20);

        $userStats = [
            'total_admins' => User::where('role', 'admin')->count(),
            'total_staff' => User::where('role', 'staff')->count(),
            'total_customers' => User::where('role', 'customer')->count()
        ];

        return view('admin.users.index', compact('users', 'userStats'));
    }

    public function bookings()
    {
        $bookings = Booking::with(['service', 'customer', 'staff'])
            ->when(request('status'), function($query, $status) {
                return $query->where('status', $status);
            })
            ->when(request('service_id'), function($query, $serviceId) {
                return $query->where('service_id', $serviceId);
            })
            ->when(request('date_from'), function($query, $dateFrom) {
                return $query->where('scheduled_at', '>=', $dateFrom);
            })
            ->when(request('date_to'), function($query, $dateTo) {
                return $query->where('scheduled_at', '<=', $dateTo);
            })
            ->latest()
            ->paginate(20);

        $services = Service::all();
        $bookingStats = [
            'total_pending' => Booking::where('status', 'pending')->count(),
            'total_confirmed' => Booking::where('status', 'confirmed')->count(),
            'total_in_progress' => Booking::where('status', 'in_progress')->count(),
            'total_completed' => Booking::where('status', 'completed')->count(),
            'total_cancelled' => Booking::where('status', 'cancelled')->count()
        ];

        return view('admin.bookings.index', compact('bookings', 'services', 'bookingStats'));
    }
}