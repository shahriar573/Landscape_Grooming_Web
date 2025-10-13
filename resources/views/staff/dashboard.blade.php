@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">👨‍🔧 Staff Dashboard</h1>
            <div class="btn-group">
                <a href="{{ route('staff.bookings') }}" class="btn btn-primary">My Bookings</a>
                <a href="{{ route('staff.schedule') }}" class="btn btn-success">Schedule</a>
                <a href="{{ route('staff.performance') }}" class="btn btn-info">Performance</a>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card admin-card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $totalAssignedBookings }}</h4>
                                <p class="card-text">Total Assigned</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-tasks fa-2x"></i>
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
                                <h4 class="card-title">{{ $completedBookings }}</h4>
                                <p class="card-text">Completed</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x"></i>
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
                                <h4 class="card-title">{{ $todayBookings }}</h4>
                                <p class="card-text">Today's Jobs</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-day fa-2x"></i>
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
                                <h4 class="card-title">${{ number_format($totalRevenue, 2) }}</h4>
                                <p class="card-text">Revenue Generated</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Schedule -->
        @if($todaySchedule->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>📅 Today's Schedule</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Service</th>
                                        <th>Customer</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todaySchedule as $booking)
                                    <tr>
                                        <td>{{ $booking->scheduled_at->format('g:i A') }}</td>
                                        <td>{{ $booking->service->name }}</td>
                                        <td>
                                            {{ $booking->customer->name }}
                                            @if($booking->customer->mobile)
                                                <br><small class="text-muted">{{ $booking->customer->mobile }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $booking->service->duration ?? 60 }} min</td>
                                        <td>
                                            <span class="badge bg-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'in_progress' ? 'warning' : 'primary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($booking->status !== 'completed' && $booking->status !== 'cancelled')
                                            <div class="btn-group btn-group-sm">
                                                <form method="POST" action="{{ route('staff.bookings.update-status', $booking) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="in_progress">
                                                    <button type="submit" class="btn btn-warning btn-sm">Start</button>
                                                </form>
                                                <form method="POST" action="{{ route('staff.bookings.update-status', $booking) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="btn btn-success btn-sm">Complete</button>
                                                </form>
                                            </div>
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

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>📋 Upcoming Bookings (Next 7 Days)</h5>
                    </div>
                    <div class="card-body">
                        @if($upcomingBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Service</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingBookings as $booking)
                                    <tr>
                                        <td>{{ $booking->scheduled_at->format('M j, g:i A') }}</td>
                                        <td>{{ Str::limit($booking->service->name, 20) }}</td>
                                        <td>{{ $booking->customer->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">No upcoming bookings in the next 7 days.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>✅ Recent Completed Jobs</h5>
                    </div>
                    <div class="card-body">
                        @if($recentCompletedBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Service</th>
                                        <th>Customer</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentCompletedBookings as $booking)
                                    <tr>
                                        <td>{{ $booking->updated_at->format('M j') }}</td>
                                        <td>{{ Str::limit($booking->service->name, 20) }}</td>
                                        <td>{{ $booking->customer->name }}</td>
                                        <td>${{ number_format($booking->price, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">No completed jobs yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Performance Chart -->
        @if($monthlyStats->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>📊 Monthly Performance</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
@if($monthlyStats->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const performanceCtx = document.getElementById('performanceChart').getContext('2d');
// Prepare PHP data as JS variables
const performanceLabels = <?= json_encode($monthlyStats->map(function($stat) { return $stat->year . '-' . str_pad($stat->month, 2, '0', STR_PAD_LEFT); })->values()) ?>;
const performanceTotalBookings = <?= json_encode($monthlyStats->pluck('total_bookings')->values()) ?>;
const performanceCompleted = <?= json_encode($monthlyStats->pluck('completed')->values()) ?>;
const performanceRevenue = <?= json_encode($monthlyStats->pluck('revenue')->values()) ?>;

const performanceChart = new Chart(performanceCtx, {
    type: 'bar',
    data: {
        labels: performanceLabels,
        datasets: [{
            label: 'Total Bookings',
            data: performanceTotalBookings,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
            yAxisID: 'y'
        }, {
            label: 'Completed Bookings',
            data: performanceCompleted,
            backgroundColor: 'rgba(75, 192, 192, 0.5)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
            yAxisID: 'y'
        }, {
            label: 'Revenue ($)',
            data: performanceRevenue,
            type: 'line',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                grid: {
                    drawOnChartArea: false,
                },
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
@endif
@endsection