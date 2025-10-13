<?php
namespace App\Http\Controllers;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }
    public function index()
    {
        $user = Auth::user();
        $query = Booking::with(['service', 'customer', 'staff']);
        if ($user) {
            if ($user->role === 'admin') {
                $bookings = $query->latest()->paginate(20);
            } elseif ($user->role === 'staff') {
                $bookings = $query->where('staff_id', $user->id)->paginate(20);
            } else {
                $bookings = $query->where('customer_id', $user->id)->paginate(20);
            }
        } else {
            $bookings = $query->where('status', 'confirmed')
                              ->where('scheduled_at', '>=', now())
                              ->paginate(12);
        }
        return view('bookings.index', compact('bookings'));
    }
    public function create(Request $request)
    {
        $services = Service::all();
        $prefill = $request->query('service_id');
        return view('bookings.create', compact('services', 'prefill'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string|max:2000'
        ]);
        $service = Service::findOrFail($data['service_id']);
        Booking::create([
            'service_id' => $service->id,
            'customer_id' => Auth::id(),
            'scheduled_at' => $data['scheduled_at'],
            'price' => $service->price,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);
        return redirect()->route('bookings.index')
            ->with('status', 'Booking created. Admin will assign staff.');
    }
    public function edit(Booking $booking)
    {
        $this->authorizeAction($booking);
        $services = Service::all();
        $staffs = User::where('role', 'staff')->get();
        return view('bookings.edit', compact('booking', 'services', 'staffs'));
    }
    public function update(Request $request, Booking $booking)
    {
        $this->authorizeAction($booking);
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string|max:2000',
            'staff_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:pending,confirmed,in_progress,completed,cancelled'
        ]);
        $booking->update($data);
        return redirect()->route('bookings.index')->with('status', 'Booking updated.');
    }
    public function destroy(Booking $booking)
    {
        $user = Auth::user();
        if (!$user || ($user->role !== 'admin' && $booking->customer_id !== $user->id)) {
            abort(403);
        }
        $booking->delete();
        return redirect()->route('bookings.index')->with('status', 'Booking deleted.');
    }
    public function assignStaff(Request $request, Booking $booking)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $data = $request->validate(['staff_id' => 'required|exists:users,id']);
        $booking->update(['staff_id' => $data['staff_id'], 'status' => 'confirmed']);
        return redirect()->route('bookings.index')->with('status', 'Staff assigned.');
    }
    protected function authorizeAction(Booking $booking)
    {
        $user = Auth::user();
        if (!$user) abort(403);
        if ($user->role === 'admin' || $booking->customer_id === $user->id) {
            return true;
        }
        abort(403);
    }
}