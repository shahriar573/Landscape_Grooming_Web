@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">My Assigned Bookings</h1>
            <div class="btn-group">
                <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
                <a href="{{ route('staff.schedule') }}" class="btn btn-success">Schedule</a>
            </div>
        </div>

        @php
            $statusCards = [
                'pending' => ['label' => 'Pending', 'color' => 'secondary'],
                'confirmed' => ['label' => 'Confirmed', 'color' => 'primary'],
                'in_progress' => ['label' => 'In Progress', 'color' => 'warning'],
                'completed' => ['label' => 'Completed', 'color' => 'success'],
                'cancelled' => ['label' => 'Cancelled', 'color' => 'danger'],
            ];
            $totalRevenue = $bookings->sum('price');
        @endphp

        <div class="row g-3 mb-4">
            @foreach($statusCards as $key => $card)
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-white bg-{{ $card['color'] }}">
                        <div class="card-body">
                            <h4 class="mb-0">{{ $bookingStats[$key] ?? 0 }}</h4>
                            <small class="text-white-50">{{ $card['label'] }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-0">${{ number_format($totalRevenue, 2) }}</h4>
                        <small class="text-muted">Revenue (this page)</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Upcoming and Active Jobs</h5>
            </div>
            <div class="card-body p-0">
                @if($bookings->count() === 0)
                    <p class="text-muted p-4 mb-0">No bookings assigned yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Scheduled</th>
                                    <th>Service</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th class="text-end">Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td>
                                            <strong>{{ $booking->scheduled_at->format('M j, Y') }}</strong>
                                            <br><small class="text-muted">{{ $booking->scheduled_at->format('g:i A') }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $booking->service->name }}</strong>
                                            @if($booking->service->duration)
                                                <br><small class="text-muted">{{ $booking->service->duration }} minutes</small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $booking->customer->name }}</strong>
                                            @if($booking->customer->mobile)
                                                <br><small class="text-muted">{{ $booking->customer->mobile }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $status = $booking->status;
                                                $statusClass = [
                                                    'pending' => 'secondary',
                                                    'confirmed' => 'primary',
                                                    'in_progress' => 'warning',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger',
                                                ][$status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                            </span>
                                        </td>
                                        <td class="text-end">${{ number_format($booking->price, 2) }}</td>
                                        <td>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#booking-notes-{{ $booking->id }}">
                                                    Details
                                                </button>
                                                @if($booking->status === 'confirmed')
                                                    <form method="POST" action="{{ route('staff.bookings.update-status', $booking) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="in_progress">
                                                        <button type="submit" class="btn btn-sm btn-warning">Start</button>
                                                    </form>
                                                @endif
                                                @if($booking->status === 'in_progress')
                                                    <form method="POST" action="{{ route('staff.bookings.update-status', $booking) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" class="btn btn-sm btn-success">Complete</button>
                                                    </form>
                                                @endif
                                                @if($booking->customer->mobile)
                                                    <a href="tel:{{ $booking->customer->mobile }}" class="btn btn-sm btn-outline-primary">Call</a>
                                                @endif
                                            </div>
                                            <div id="booking-notes-{{ $booking->id }}" class="collapse mt-2">
                                                <div class="card card-body">
                                                    <dl class="row mb-0">
                                                        <dt class="col-sm-4">Service</dt>
                                                        <dd class="col-sm-8">{{ $booking->service->name }}</dd>
                                                        <dt class="col-sm-4">Customer</dt>
                                                        <dd class="col-sm-8">{{ $booking->customer->email }}</dd>
                                                        @if($booking->notes)
                                                            <dt class="col-sm-4">Notes</dt>
                                                            <dd class="col-sm-8">{{ $booking->notes }}</dd>
                                                        @endif
                                                    </dl>
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
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection