<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Booking;
use App\Models\Service;
class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalBookings = Booking::count();
        $totalRevenue = Booking::sum('price');
        return view('dashboard', compact('totalUsers', 'totalBookings', 'totalRevenue'));
    }
}
