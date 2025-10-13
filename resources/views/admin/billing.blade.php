@extends('layouts.app')

@section('title', 'Billing Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">💰 Billing Management</h1>
            <div class="btn-group">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Dashboard</a>
                <button class="btn btn-primary" onclick="generateReport()">
                    <i class="fas fa-file-export"></i> Export Report
                </button>
            </div>
        </div>

        <!-- Revenue Overview Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card admin-card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">${{ number_format($todayRevenue ?? 0, 2) }}</h4>
                                <p class="card-text">Today's Revenue</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-day fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">${{ number_format($monthlyRevenue ?? 0, 2) }}</h4>
                                <p class="card-text">This Month</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-alt fa-2x"></i>
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
                                <h4 class="card-title">${{ number_format($pendingPayments ?? 0, 2) }}</h4>
                                <p class="card-text">Pending Payments</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x"></i>
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
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Status Summary -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>📊 Revenue Trends (Last 6 Months)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>💳 Payment Status Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.billing') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Customer or booking ID...">
                    </div>
                    <div class="col-md-2">
                        <label for="payment_status" class="form-label">Payment Status</label>
                        <select class="form-select" id="payment_status" name="payment_status">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="overdue" {{ request('payment_status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="sort" class="form-label">Sort By</label>
                        <div class="input-group">
                            <select class="form-select" id="sort" name="sort">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date</option>
                                <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Amount</option>
                                <option value="customer" {{ request('sort') == 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="payment_status" {{ request('sort') == 'payment_status' ? 'selected' : '' }}>Status</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Billing Table -->
        <div class="card">
            <div class="card-header">
                <h5>Billing Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Payment Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr>
                                <td>
                                    <strong>#{{ $booking->id }}</strong>
                                    @if($booking->payment_method)
                                        <br><small class="text-muted">{{ ucfirst($booking->payment_method) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $booking->customer->name }}</strong>
                                        <br><small class="text-muted">{{ $booking->customer->email }}</small>
                                    </div>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($booking->service->name, 30) }}</td>
                                <td>{{ $booking->scheduled_at->format('M j, Y g:i A') }}</td>
                                <td>
                                    <strong>${{ number_format($booking->price, 2) }}</strong>
                                    @if($booking->discount_amount > 0)
                                        <br><small class="text-success">-${{ number_format($booking->discount_amount, 2) }} discount</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $booking->payment_status == 'paid' ? 'success' : 
                                        ($booking->payment_status == 'pending' ? 'warning' : 
                                        ($booking->payment_status == 'overdue' ? 'danger' : 'secondary')) 
                                    }}">
                                        {{ ucfirst($booking->payment_status) }}
                                    </span>
                                    @if($booking->payment_date)
                                        <br><small class="text-muted">Paid: {{ $booking->payment_date->format('M j, Y') }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" data-bs-toggle="modal" 
                                                data-bs-target="#viewBillingModal{{ $booking->id }}" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($booking->payment_status == 'pending' || $booking->payment_status == 'overdue')
                                        <form method="POST" action="{{ route('admin.billing.mark-paid', $booking) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success" title="Mark as Paid">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @if($booking->payment_status == 'paid')
                                        <button class="btn btn-warning" onclick="processRefund(<?= $booking->id ?>)" title="Process Refund">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        @endif
                                        <button class="btn btn-primary" onclick="generateInvoice(<?= $booking->id ?>)" title="Generate Invoice">
                                            <i class="fas fa-file-invoice"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- View Billing Modal -->
                            <div class="modal fade" id="viewBillingModal{{ $booking->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Billing Details - #{{ $booking->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <strong>Customer:</strong><br>
                                                    {{ $booking->customer->name }}<br>
                                                    {{ $booking->customer->email }}<br>
                                                    {{ $booking->customer->mobile }}
                                                </div>
                                                <div class="col-6">
                                                    <strong>Service:</strong><br>
                                                    {{ $booking->service->name }}<br>
                                                    <small class="text-muted">{{ $booking->service->description }}</small>
                                                </div>
                                                <div class="col-6">
                                                    <strong>Scheduled:</strong><br>
                                                    {{ $booking->scheduled_at->format('M j, Y g:i A') }}
                                                </div>
                                                <div class="col-6">
                                                    <strong>Status:</strong><br>
                                                    <span class="badge bg-info">{{ ucfirst($booking->status) }}</span>
                                                </div>
                                                <div class="col-12">
                                                    <hr>
                                                    <h6>Billing Summary:</h6>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <td>Service Price:</td>
                                                            <td class="text-end">${{ number_format($booking->service->price, 2) }}</td>
                                                        </tr>
                                                        @if($booking->discount_amount > 0)
                                                        <tr>
                                                            <td>Discount:</td>
                                                            <td class="text-end text-success">-${{ number_format($booking->discount_amount, 2) }}</td>
                                                        </tr>
                                                        @endif
                                                        <tr class="fw-bold">
                                                            <td>Total:</td>
                                                            <td class="text-end">${{ number_format($booking->price, 2) }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                @if($booking->notes)
                                                <div class="col-12">
                                                    <strong>Notes:</strong><br>
                                                    <p class="text-muted">{{ $booking->notes }}</p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary" onclick="generateInvoice(<?= $booking->id ?>)">
                                                Generate Invoice
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No billing records found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($bookings->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $bookings->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
 const revenueLabels = <?= json_encode($revenueLabels ?? []) ?>;
    const revenueData = <?= json_encode($revenueData ?? []) ?>;

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueLabels,
        datasets: [{
            label: 'Revenue ($)',
            data: revenueData,
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            fill: true
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

// Payment Status Chart
const statusCtx = document.getElementById('paymentStatusChart').getContext('2d');
const statusData = <?= json_encode([
    $paymentStatusStats['paid'] ?? 0,
    $paymentStatusStats['pending'] ?? 0,
    $paymentStatusStats['overdue'] ?? 0,
    $paymentStatusStats['refunded'] ?? 0
]); ?>;
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Paid', 'Pending', 'Overdue', 'Refunded'],
        datasets: [{
            data: statusData,
            backgroundColor: [
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(153, 102, 255, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});

function generateReport() {
    // Implementation for generating billing reports
    alert('Generating billing report...');
}

function processRefund(bookingId) {
    if (confirm('Are you sure you want to process a refund for this booking?')) {
        // Implementation for processing refunds
        alert('Processing refund for booking #' + bookingId);
    }
}

function generateInvoice(bookingId) {
    // Implementation for generating invoices
    window.open('/admin/billing/invoice/' + bookingId, '_blank');
}
</script>
@endsection