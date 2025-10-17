@extends('layouts.app')

@section('title', 'Admin - Bookings Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">📅 Bookings Management</h1>
            <div class="btn-group">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
                <a href="{{ route('admin.services') }}" class="btn btn-outline-primary">Services</a>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-info">Users</a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-2">
                <div class="card border-0 shadow-sm text-white bg-secondary">
                    <div class="card-body text-center">
                        <h5 class="mb-0">{{ $bookingStats['total_pending'] }}</h5>
                        <small>Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm text-white bg-primary">
                    <div class="card-body text-center">
                        <h5 class="mb-0">{{ $bookingStats['total_confirmed'] }}</h5>
                        <small>Confirmed</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm text-white bg-warning">
                    <div class="card-body text-center">
                        <h5 class="mb-0">{{ $bookingStats['total_in_progress'] }}</h5>
                        <small>In Progress</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm text-white bg-success">
                    <div class="card-body text-center">
                        <h5 class="mb-0">{{ $bookingStats['total_completed'] }}</h5>
                        <small>Completed</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm text-white bg-danger">
                    <div class="card-body text-center">
                        <h5 class="mb-0">{{ $bookingStats['total_cancelled'] }}</h5>
                        <small>Cancelled</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="mb-0">${{ number_format($bookings->sum('price'), 2) }}</h5>
                        <small class="text-muted">Total Value</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.bookings') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="service_id" class="form-label">Service</label>
                            <select name="service_id" id="service_id" class="form-select">
                                <option value="">All Services</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="w-100">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Bookings</h5>
                <span class="badge bg-secondary">{{ $bookings->total() }} total</span>
            </div>
            <div class="card-body p-0">
                @if($bookings->count() === 0)
                    <div class="p-4 text-muted text-center">
                        No bookings found matching your criteria.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Scheduled</th>
                                    <th>Service</th>
                                    <th>Customer</th>
                                    <th>Staff Assignment</th>
                                    <th>Status</th>
                                    <th class="text-end">Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td>
                                            <strong>#{{ $booking->id }}</strong>
                                            <br><small class="text-muted">{{ $booking->created_at->format('M j') }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $booking->scheduled_at->format('M j, Y') }}</strong>
                                            <br><small class="text-muted">{{ $booking->scheduled_at->format('g:i A') }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $booking->service->name }}</strong>
                                            @if($booking->service->duration)
                                                <br><small class="text-muted">{{ $booking->service->duration }} min</small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $booking->customer->name }}</strong>
                                            <br><small class="text-muted">{{ $booking->customer->email }}</small>
                                            @if($booking->customer->mobile)
                                                <br><small class="text-muted">📱 {{ $booking->customer->mobile }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($booking->staff)
                                                <span class="badge bg-success">
                                                    👤 {{ $booking->staff->name }}
                                                </span>
                                                <br><small class="text-muted">{{ $booking->staff->email }}</small>
                                            @else
                                                <span class="badge bg-warning text-dark">⚠️ Unassigned</span>
                                                <br>
                                                <form method="POST" action="{{ route('bookings.assign-staff', $booking) }}" class="mt-1">
                                                    @csrf
                                                    <div class="input-group input-group-sm">
                                                        <select name="staff_id" class="form-select form-select-sm" required aria-label="Assign staff member">
                                                            <option value="">Select Staff...</option>
                                                            @php
                                                                $availableStaff = \App\Models\User::where('role', 'staff')->get();
                                                            @endphp
                                                            @foreach($availableStaff as $staff)
                                                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="btn btn-outline-primary btn-sm">Assign</button>
                                                    </div>
                                                </form>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'secondary',
                                                    'confirmed' => 'primary',
                                                    'in_progress' => 'warning',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger',
                                                ];
                                                $statusColor = $statusColors[$booking->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">
                                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <strong>${{ number_format($booking->price, 2) }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 flex-wrap">
                                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="collapse" data-bs-target="#details-{{ $booking->id }}">
                                                    👁️
                                                </button>
                                                @if($booking->customer->mobile)
                                                    <a href="tel:{{ $booking->customer->mobile }}" class="btn btn-sm btn-outline-success" title="Call Customer">
                                                        📞
                                                    </a>
                                                @endif
                                                <a href="mailto:{{ $booking->customer->email }}" class="btn btn-sm btn-outline-primary" title="Email Customer">
                                                    ✉️
                                                </a>
                                            </div>
                                            <!-- Expandable Details -->
                                            <div id="details-{{ $booking->id }}" class="collapse mt-2">
                                                <div class="card card-body">
                                                    <dl class="row mb-0 small">
                                                        <dt class="col-4">Booking ID:</dt>
                                                        <dd class="col-8">#{{ $booking->id }}</dd>
                                                        
                                                        <dt class="col-4">Created:</dt>
                                                        <dd class="col-8">{{ $booking->created_at->format('M j, Y g:i A') }}</dd>
                                                        
                                                        @if($booking->notes)
                                                            <dt class="col-4">Notes:</dt>
                                                            <dd class="col-8">{{ $booking->notes }}</dd>
                                                        @endif
                                                        
                                                        <dt class="col-4">Service Details:</dt>
                                                        <dd class="col-8">
                                                            {{ $booking->service->description ?? 'No description' }}
                                                        </dd>
                                                    </dl>
                                                    
                                                    <!-- Staff Assignment Actions -->
                                                    @if($booking->staff && in_array($booking->status, ['pending', 'confirmed']))
                                                        <hr>
                                                        <div class="d-flex gap-2">
                                                            <form method="POST" action="{{ route('bookings.assign-staff', $booking) }}">
                                                                @csrf
                                                                <input type="hidden" name="staff_id" value="">
                                                                <button type="submit" class="btn btn-sm btn-outline-warning">Unassign Staff</button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @if($bookings->hasPages())
                <div class="card-footer">
                    {{ $bookings->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit filter form when selection changes
    const filterForm = document.querySelector('form');
    const selects = filterForm.querySelectorAll('select');
    
    selects.forEach(select => {
        select.addEventListener('change', () => {
            filterForm.submit();
        });
    });
    
    // Confirm staff assignment changes
    const assignForms = document.querySelectorAll('form[action*="assign-staff"]');
    assignForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const staffSelect = form.querySelector('select[name="staff_id"]');
            const staffName = staffSelect ? staffSelect.options[staffSelect.selectedIndex].text : 'Unassign';
            
            if (!confirm(`Are you sure you want to ${staffName === 'Unassign' ? 'unassign staff from' : 'assign ' + staffName + ' to'} this booking?`)) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection