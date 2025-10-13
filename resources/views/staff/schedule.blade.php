@extends('layouts.app')

@section('title', 'My Schedule')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">📅 My Schedule</h1>
            <div class="btn-group">
                <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">Dashboard</a>
                <a href="{{ route('staff.bookings') }}" class="btn btn-primary">My Bookings</a>
            </div>
        </div>

        <!-- Schedule Navigation -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h5 class="mb-0">{{ $currentDate->format('F Y') }}</h5>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="btn-group">
                            <a href="{{ route('staff.schedule', ['date' => $currentDate->copy()->subWeek()->format('Y-m-d')]) }}" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-chevron-left"></i> Previous Week
                            </a>
                            <a href="{{ route('staff.schedule') }}" class="btn btn-primary">Today</a>
                            <a href="{{ route('staff.schedule', ['date' => $currentDate->copy()->addWeek()->format('Y-m-d')]) }}" 
                               class="btn btn-outline-primary">
                                Next Week <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="btn-group">
                            <button class="btn btn-sm {{ $view === 'week' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <a href="{{ route('staff.schedule', ['view' => 'week', 'date' => $currentDate->format('Y-m-d')]) }}" 
                                   class="text-decoration-none {{ $view === 'week' ? 'text-white' : '' }}">Week</a>
                            </button>
                            <button class="btn btn-sm {{ $view === 'month' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <a href="{{ route('staff.schedule', ['view' => 'month', 'date' => $currentDate->format('Y-m-d')]) }}" 
                                   class="text-decoration-none {{ $view === 'month' ? 'text-white' : '' }}">Month</a>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($view === 'week')
        <!-- Weekly View -->
        <div class="card">
            <div class="card-header">
                <h5>Week of {{ $weekStart->format('M j') }} - {{ $weekEnd->format('M j, Y') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Time</th>
                                @foreach($weekDays as $day)
                                <th class="text-center {{ $day->isToday() ? 'bg-light' : '' }}">
                                    <div>{{ $day->format('D') }}</div>
                                    <div class="h6 mb-0">{{ $day->format('j') }}</div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeSlots as $time)
                            <tr>
                                <td class="fw-bold text-muted">{{ $time }}</td>
                                @foreach($weekDays as $day)
                                <td class="position-relative {{ $day->isToday() ? 'bg-light' : '' }}" style="height: 80px;">
                                    @php
                                        $dayBookings = $weeklyBookings->filter(function($booking) use ($day, $time) {
                                            return $booking->scheduled_at->format('Y-m-d') === $day->format('Y-m-d') && 
                                                   $booking->scheduled_at->format('H:i') === $time;
                                        });
                                    @endphp
                                    @foreach($dayBookings as $booking)
                                    <div class="booking-slot bg-{{ 
                                        $booking->status === 'completed' ? 'success' : 
                                        ($booking->status === 'in_progress' ? 'warning' : 
                                        ($booking->status === 'confirmed' ? 'primary' : 'secondary')) 
                                    }} text-white p-1 rounded mb-1 small">
                                        <div class="fw-bold">{{ Str::limit($booking->service->name, 15) }}</div>
                                        <div>{{ $booking->customer->name }}</div>
                                        @if($booking->customer->mobile)
                                        <div><i class="fas fa-phone"></i> {{ $booking->customer->mobile }}</div>
                                        @endif
                                    </div>
                                    @endforeach
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <!-- Monthly View -->
        <div class="card">
            <div class="card-header">
                <h5>{{ $currentDate->format('F Y') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                    <div class="col text-center fw-bold py-2">{{ $dayName }}</div>
                    @endforeach
                </div>
                @foreach($calendarWeeks as $week)
                <div class="row border-top">
                    @foreach($week as $day)
                    <div class="col p-2 border-end" style="height: 120px;">
                        @if($day)
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-bold {{ $day->isToday() ? 'text-primary' : ($day->format('m') != $currentDate->format('m') ? 'text-muted' : '') }}">
                                {{ $day->format('j') }}
                            </span>
                        </div>
                        @php
                            $dayBookings = $monthlyBookings->filter(function($booking) use ($day) {
                                return $booking->scheduled_at->format('Y-m-d') === $day->format('Y-m-d');
                            });
                        @endphp
                        @foreach($dayBookings->take(2) as $booking)
                        <div class="booking-dot bg-{{ 
                            $booking->status === 'completed' ? 'success' : 
                            ($booking->status === 'in_progress' ? 'warning' : 
                            ($booking->status === 'confirmed' ? 'primary' : 'secondary')) 
                        }} text-white px-1 py-0 rounded mb-1 small" 
                             data-bs-toggle="tooltip" 
                             title="{{ $booking->scheduled_at->format('g:i A') }} - {{ $booking->service->name }} ({{ $booking->customer->name }})">
                            {{ $booking->scheduled_at->format('g:i A') }} {{ Str::limit($booking->service->name, 8) }}
                        </div>
                        @endforeach
                        @if($dayBookings->count() > 2)
                        <div class="small text-muted">+{{ $dayBookings->count() - 2 }} more</div>
                        @endif
                        @endif
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Today's Summary -->
        @if($todaysBookings->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5>📋 Today's Schedule Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($todaysBookings as $booking)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border-{{ 
                            $booking->status === 'completed' ? 'success' : 
                            ($booking->status === 'in_progress' ? 'warning' : 
                            ($booking->status === 'confirmed' ? 'primary' : 'secondary')) 
                        }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title">{{ $booking->scheduled_at->format('g:i A') }}</h6>
                                    <span class="badge bg-{{ 
                                        $booking->status === 'completed' ? 'success' : 
                                        ($booking->status === 'in_progress' ? 'warning' : 
                                        ($booking->status === 'confirmed' ? 'primary' : 'secondary')) 
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </div>
                                <p class="card-text">
                                    <strong>{{ $booking->service->name }}</strong><br>
                                    <small class="text-muted">{{ $booking->customer->name }}</small><br>
                                    @if($booking->customer->mobile)
                                    <small><i class="fas fa-phone"></i> {{ $booking->customer->mobile }}</small>
                                    @endif
                                </p>
                                <div class="btn-group btn-group-sm w-100">
                                    @if($booking->status === 'confirmed')
                                    <form method="POST" action="{{ route('staff.bookings.update-status', $booking) }}" class="flex-fill">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="btn btn-warning w-100">Start</button>
                                    </form>
                                    @endif
                                    @if($booking->status === 'in_progress')
                                    <form method="POST" action="{{ route('staff.bookings.update-status', $booking) }}" class="flex-fill">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-success w-100">Complete</button>
                                    </form>
                                    @endif
                                    @if($booking->customer->mobile)
                                    <a href="tel:{{ $booking->customer->mobile }}" class="btn btn-primary flex-fill">
                                        <i class="fas fa-phone"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Legend -->
        <div class="card mt-4">
            <div class="card-body">
                <h6>Status Legend:</h6>
                <div class="row">
                    <div class="col-md-3">
                        <span class="badge bg-primary me-2">Confirmed</span> Ready to start
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-warning me-2">In Progress</span> Currently working
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-success me-2">Completed</span> Job finished
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-secondary me-2">Cancelled</span> Cancelled booking
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.booking-slot {
    font-size: 0.75rem;
    line-height: 1.2;
}

.booking-dot {
    font-size: 0.7rem;
    line-height: 1.1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.table td {
    vertical-align: top;
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}

.border-top {
    border-top: 1px solid #dee2e6 !important;
}
</style>
@endpush

@push('scripts')
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush