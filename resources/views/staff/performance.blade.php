@extends('layouts.app')

@section('title', 'My Performance')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">📊 My Performance</h1>
            <div class="btn-group">
                <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">Dashboard</a>
                <a href="{{ route('staff.bookings') }}" class="btn btn-primary">My Bookings</a>
            </div>
        </div>

        <!-- Performance Overview Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card admin-card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $totalJobs }}</h4>
                                <p class="card-text">Total Jobs</p>
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
                                <h4 class="card-title">{{ $completedJobs }}</h4>
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
                <div class="card admin-card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ number_format($completionRate, 1) }}%</h4>
                                <p class="card-text">Completion Rate</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-percentage fa-2x"></i>
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

        <!-- Performance Charts -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>📈 Monthly Performance Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceTrendChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>🎯 Job Status Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusDistributionChart"></canvas>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between">
                                <span>Completion Rate:</span>
                                <strong>{{ number_format($completionRate, 1) }}%</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>On-Time Rate:</span>
                                <strong>{{ number_format($onTimeRate ?? 95, 1) }}%</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Performance -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🛠️ Top Services by Volume</h5>
                    </div>
                    <div class="card-body">
                        @if($topServices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Jobs</th>
                                        <th>Revenue</th>
                                        <th>Success Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topServices as $service)
                                    <tr>
                                        <td>{{ Str::limit($service->name, 25) }}</td>
                                        <td><span class="badge bg-primary">{{ $service->jobs_count }}</span></td>
                                        <td>${{ number_format($service->revenue, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $service->success_rate >= 90 ? 'success' : ($service->success_rate >= 75 ? 'warning' : 'danger') }}">
                                                {{ number_format($service->success_rate, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">No service data available yet.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>⭐ Customer Satisfaction</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <h2 class="text-primary">{{ number_format($avgRating ?? 4.5, 1) }}/5.0</h2>
                            <div class="text-warning mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= ($avgRating ?? 4.5) ? '' : '-o' }}"></i>
                                @endfor
                            </div>
                            <p class="text-muted">Based on {{ $totalRatings ?? 0 }} customer reviews</p>
                        </div>
                        
                        @if($recentReviews && $recentReviews->count() > 0)
                        <div class="border-top pt-3">
                            <h6>Recent Reviews:</h6>
                            @foreach($recentReviews->take(3) as $review)
                            <div class="mb-2 p-2 bg-light rounded">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $review->customer->name }}</strong>
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }} small"></i>
                                        @endfor
                                    </div>
                                </div>
                                <small class="text-muted">{{ Str::limit($review->comment, 80) }}</small>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-muted">No reviews yet. Keep up the great work!</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>📅 Recent Activity (Last 30 Days)</h5>
            </div>
            <div class="card-body">
                @if($recentBookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Service</th>
                                <th>Customer</th>
                                <th>Duration</th>
                                <th>Revenue</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                            <tr>
                                <td>{{ $booking->scheduled_at->format('M j, Y') }}</td>
                                <td>{{ Str::limit($booking->service->name, 25) }}</td>
                                <td>{{ $booking->customer->name }}</td>
                                <td>
                                    @if($booking->actual_duration)
                                        {{ $booking->actual_duration }} min
                                        @if($booking->service->duration && $booking->actual_duration <= $booking->service->duration)
                                            <small class="text-success">(On time)</small>
                                        @elseif($booking->service->duration)
                                            <small class="text-warning">({{ $booking->actual_duration - $booking->service->duration }}min over)</small>
                                        @endif
                                    @else
                                        {{ $booking->service->duration ?? 60 }} min (est.)
                                    @endif
                                </td>
                                <td>${{ number_format($booking->price, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $booking->status === 'completed' ? 'success' : 
                                        ($booking->status === 'in_progress' ? 'warning' : 
                                        ($booking->status === 'confirmed' ? 'primary' : 'secondary')) 
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted">No recent activity to display.</p>
                @endif
            </div>
        </div>

        <!-- Performance Tips -->
        <div class="card">
            <div class="card-header">
                <h5>💡 Performance Tips</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fa-2x text-primary mb-2"></i>
                                <h6>Time Management</h6>
                                <p class="small text-muted">Complete jobs within estimated time to improve efficiency ratings.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i class="fas fa-comments fa-2x text-success mb-2"></i>
                                <h6>Communication</h6>
                                <p class="small text-muted">Keep customers informed about progress and any delays.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <i class="fas fa-star fa-2x text-info mb-2"></i>
                                <h6>Quality Focus</h6>
                                <p class="small text-muted">Attention to detail leads to better customer satisfaction.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Prepare PHP data for JS
const trendLabels = <?= json_encode($monthlyStats->map(function($stat) { return $stat->year . '-' . str_pad($stat->month, 2, '0', STR_PAD_LEFT); })->toArray()) ?>;
const trendCompleted = <?= json_encode($monthlyStats->pluck('completed')->toArray()) ?>;
const trendRevenue = <?= json_encode($monthlyStats->pluck('revenue')->toArray()) ?>;

// Performance Trend Chart
const trendCtx = document.getElementById('performanceTrendChart').getContext('2d');
const trendChart = new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: trendLabels,
        datasets: [{
            label: 'Jobs Completed',
            data: trendCompleted,
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            yAxisID: 'y'
        }, {
            label: 'Revenue ($)',
            data: trendRevenue,
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            borderColor: 'rgba(75, 192, 192, 1)',
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

// Status Distribution Chart
const statusCtx = document.getElementById('statusDistributionChart').getContext('2d');
const statusData = <?= json_encode([
    ($statusStats['completed'] ?? 0),
    ($statusStats['in_progress'] ?? 0),
    ($statusStats['confirmed'] ?? 0),
    ($statusStats['cancelled'] ?? 0),
]) ?>;
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'In Progress', 'Confirmed', 'Cancelled'],
        datasets: [{
            data: statusData,
            backgroundColor: [
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 99, 132, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});
</script>
@endsection