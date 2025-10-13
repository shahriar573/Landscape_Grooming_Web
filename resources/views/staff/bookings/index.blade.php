@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">📋 My Assigned Bookings</h1>
            <div class="btn-group">
                <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">Dashboard</a>
                <a href="{{ route('staff.schedule') }}" class="btn btn-success">Schedule</a>
            </div>
        </div>

        <!-- Status Overview Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card admin-card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $pendingBookings }}</h4>
                                <p class="card-text">Pending</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x"></i>
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
                                <h4 class="card-title">{{ $inProgressBookings }}</h4>
                                <p class="card-text">In Progress</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-play fa-2x"></i>
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

        <!-- Filters and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('staff.bookings') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Bookings</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Customer name or service...">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                    <div class="col-md-2">
                        <div class="d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="card">
            <div class="card-header">
                <h5>My Bookings</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Service</th>
                                <th>Customer</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr>
                                <td>
                                    <strong>{{ $booking->scheduled_at->format('M j, Y') }}</strong>
                                    <br><small class="text-muted">{{ $booking->scheduled_at->format('g:i A') }}</small>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $booking->service->name }}</strong>
                                        @if($booking->service->duration)
                                            <br><small class="text-muted">{{ $booking->service->duration }} min</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $booking->customer->name }}</strong>
                                        @if($booking->customer->mobile)
                                            <br><small class="text-muted">
                                                <i class="fas fa-phone"></i> {{ $booking->customer->mobile }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($booking->address)
                                        <small>{{ Str::limit($booking->address, 30) }}</small>
                                    @elseif($booking->customer->address)
                                        <small>{{ Str::limit($booking->customer->address, 30) }}</small>
                                    @else
                                        <small class="text-muted">No address</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $booking->status === 'completed' ? 'success' : 
                                        ($booking->status === 'in_progress' ? 'warning' : 
                                        ($booking->status === 'confirmed' ? 'primary' : 'secondary')) 
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <strong>${{ number_format($booking->price, 2) }}</strong>
                                        <br><span class="badge bg-{{ 
                                            $booking->payment_status === 'paid' ? 'success' : 
                                            ($booking->payment_status === 'pending' ? 'warning' : 'danger') 
                                        }}">
                                            {{ ucfirst($booking->payment_status) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" data-bs-toggle="modal" 
                                                data-bs-target="#viewBookingModal{{ $booking->id }}" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($booking->status === 'confirmed')
                                        <form method="POST" action="{{ route('staff.bookings.update-status', $booking) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="in_progress">
                                            <button type="submit" class="btn btn-warning" title="Start Job">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @if($booking->status === 'in_progress')
                                        <form method="POST" action="{{ route('staff.bookings.update-status', $booking) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-success" title="Complete Job">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @if($booking->customer->mobile)
                                        <a href="tel:{{ $booking->customer->mobile }}" class="btn btn-primary" title="Call Customer">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- View Booking Modal -->
                            <div class="modal fade" id="viewBookingModal{{ $booking->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Booking Details - #{{ $booking->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <h6>Customer Information</h6>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <td><strong>Name:</strong></td>
                                                            <td>{{ $booking->customer->name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Email:</strong></td>
                                                            <td>{{ $booking->customer->email }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Mobile:</strong></td>
                                                            <td>{{ $booking->customer->mobile ?? 'Not provided' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Address:</strong></td>
                                                            <td>{{ $booking->address ?? $booking->customer->address ?? 'Not provided' }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Service Details</h6>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <td><strong>Service:</strong></td>
                                                            <td>{{ $booking->service->name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Scheduled:</strong></td>
                                                            <td>{{ $booking->scheduled_at->format('M j, Y g:i A') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Duration:</strong></td>
                                                            <td>{{ $booking->service->duration ?? 60 }} minutes</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Price:</strong></td>
                                                            <td>${{ number_format($booking->price, 2) }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                @if($booking->notes)
                                                <div class="col-12">
                                                    <h6>Special Instructions</h6>
                                                    <p class="text-muted">{{ $booking->notes }}</p>
                                                </div>
                                                @endif
                                                @if($booking->service->description)
                                                <div class="col-12">
                                                    <h6>Service Description</h6>
                                                    <p class="text-muted">{{ $booking->service->description }}</p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            @if($booking->customer->mobile)
                                            <a href="tel:{{ $booking->customer->mobile }}" class="btn btn-primary">
                                                <i class="fas fa-phone"></i> Call Customer
                                            </a>
                                            @endif
                                            @if($booking->status === 'confirmed' || $booking->status === 'in_progress')
                                            <div class="btn-group">
                                                @if($booking->status === 'confirmed')
                                                <form method="POST" action="{{ route('staff.bookings.update-status', $booking) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="in_progress">
                                                    <button type="submit" class="btn btn-warning">Start Job</button>
                                                </form>
                                                @endif
                                                @if($booking->status === 'in_progress')
                                                <form method="POST" action="{{ route('staff.bookings.update-status', $booking) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="btn btn-success">Complete Job</button>
                                                </form>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No bookings assigned to you yet.
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