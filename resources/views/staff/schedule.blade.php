@extends('layouts.app')

@section('title', 'My Schedule')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">My Schedule</h1>
            <div class="btn-group">
                <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
                <a href="{{ route('staff.bookings') }}" class="btn btn-outline-primary">My Bookings</a>
            </div>
        </div>

        @php
            $events = collect($bookings)->map(function ($event) {
                $start = \Carbon\Carbon::parse($event['start']);
                $end = isset($event['end']) ? \Carbon\Carbon::parse($event['end']) : null;

                return [
                    'start' => $start,
                    'end' => $end,
                    'service' => $event['extendedProps']['service'] ?? $event['title'],
                    'customer' => $event['extendedProps']['customer'] ?? null,
                    'status' => $event['extendedProps']['status'] ?? null,
                    'notes' => $event['extendedProps']['notes'] ?? null,
                    'price' => $event['extendedProps']['price'] ?? null,
                ];
            })->groupBy(fn ($event) => $event['start']->format('Y-m-d'))->sortKeys();

            $statusColors = [
                'pending' => 'secondary',
                'confirmed' => 'primary',
                'in_progress' => 'warning',
                'completed' => 'success',
                'cancelled' => 'danger',
            ];
        @endphp

        @if($events->isEmpty())
            <div class="card">
                <div class="card-body text-muted">
                    No upcoming bookings on your schedule yet.
                </div>
            </div>
        @else
            @foreach($events as $date => $dayEvents)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</strong>
                        <span class="badge bg-light text-dark">{{ $dayEvents->count() }} job{{ $dayEvents->count() === 1 ? '' : 's' }}</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($dayEvents->sortBy('start') as $entry)
                                @php
                                    $badge = $statusColors[$entry['status'] ?? 'pending'] ?? 'secondary';
                                @endphp
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between flex-wrap gap-2">
                                        <div>
                                            <strong>{{ $entry['service'] }}</strong>
                                            <div class="text-muted">
                                                {{ $entry['start']->format('g:i A') }}
                                                @if($entry['end'])
                                                    – {{ $entry['end']->format('g:i A') }}
                                                @endif
                                            </div>
                                            @if($entry['customer'])
                                                <div class="small">Client: {{ $entry['customer'] }}</div>
                                            @endif
                                            @if($entry['notes'])
                                                <div class="text-muted small">Notes: {{ $entry['notes'] }}</div>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $badge }}">
                                                {{ ucfirst(str_replace('_', ' ', $entry['status'] ?? 'pending')) }}
                                            </span>
                                            @if($entry['price'])
                                                <div class="text-muted small mt-1">${{ number_format($entry['price'], 2) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection