@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">🔧 Admin Dashboard</h1>
            <div class="btn-group">
                <a href="{{ route('admin.bookings') }}" class="btn btn-warning">Bookings</a>
                <a href="{{ route('admin.services') }}" class="btn btn-primary">Services</a>
                <a href="{{ route('admin.billing') }}" class="btn btn-success">Billing</a>
                <a href="{{ route('admin.users') }}" class="btn btn-info">Users</a>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card admin-card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ number_format($totalUsers ?? 0) }}</h4>
                                <p class="card-text">Total Users</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ number_format($totalServices ?? 0) }}</h4>
                                <p class="card-text">Services</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-concierge-bell fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ number_format($totalBookings ?? 0) }}</h4>
                                <p class="card-text">Total Bookings</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card text-white bg-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">${{ number_format($totalRevenue ?? 0, 2) }}</h4>
                                <p class="card-text">Total Revenue</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Monthly Revenue</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Booking Status Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Tables Row -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Bookings</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Service</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentBookings ?? [] as $booking)
                                    <tr>
                                        <td>{{ $booking->customer->name }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($booking->service->name, 20) }}</td>
                                        <td>{{ $booking->scheduled_at->format('M j, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'primary') }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                        <td>${{ number_format($booking->price, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No recent bookings</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Service Performance</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Bookings</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $serviceStatsCollection = $serviceStats ?? collect([]);
                                    @endphp
                                    @forelse($serviceStatsCollection->take(5) as $service)
                                    <tr>
                                        <td>{{ \Illuminate\Support\Str::limit($service['name'], 25) }}</td>
                                        <td>{{ $service['bookings_count'] }}</td>
                                        <td>${{ number_format($service['total_revenue'], 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No service data available</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Bookings List (compact) -->
        <div class="row mt-4">
            <div class="col-12">
                @php
                    // If a $bookings variable wasn't passed by the controller, build a compact fallback list
                    $bookings = isset($bookings) ? $bookings : \App\Models\Booking::with(['service','customer','staff'])->latest()->paginate(10);
                @endphp
                @include('admin.bookings._table', ['bookings' => $bookings])
                <div class="mt-2 text-end">
                    <a href="{{ route('admin.bookings') }}" class="btn btn-outline-secondary btn-sm">Open full bookings page</a>
                </div>
            </div>
        </div>

        <!-- Staff Performance -->
        @php
            $staffStatsCollection = $staffStats ?? collect([]);
        @endphp
        @if($staffStatsCollection->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Staff Performance</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Staff Member</th>
                                        <th>Completed Bookings</th>
                                        <th>Total Revenue Generated</th>
                                        <th>Average per Booking</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staffStatsCollection as $staff)
                                    <tr>
                                        <td>{{ $staff['name'] }}</td>
                                        <td>{{ $staff['completed_bookings'] }}</td>
                                        <td>${{ number_format($staff['total_revenue'], 2) }}</td>
                                        <td>
                                            @if($staff['completed_bookings'] > 0)
                                                ${{ number_format($staff['total_revenue'] / $staff['completed_bookings'], 2) }}
                                            @else
                                                $0.00
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart data prepared server-side via inline Blade expressions to avoid @php blocks
   const revenueConnLabels = <?= json_encode(
       collect($monthlyRevenue ?? [])
           ->map(fn($month) => 
               $month->year . '-' . str_pad($month->month, 2, '0', STR_PAD_LEFT)
           )
           ->values()
   ); ?>;

    const revenueConnData = <?= json_encode(
        collect($monthlyRevenue ?? [])
            ->map(fn($month) => $month->revenue)
            ->values()
    ); ?>

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueConnLabels,
        datasets: [{
            label: 'Revenue',
            data: revenueConnData,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Status Distribution Chart
const statusLabels = <?= json_encode(collect($bookingStatusStats ?? [])->keys()->map(fn($k) => ucfirst($k))->values()); ?>;
const statusData = <?= json_encode(collect($bookingStatusStats ?? [])->values()->all()); ?>;   

const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusData,
            backgroundColor: [
                '#fbbf24', // pending
                '#3b82f6', // confirmed
                '#8b5cf6', // in_progress
                '#10b981', // completed
                '#ef4444'  // cancelled
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
@endsection