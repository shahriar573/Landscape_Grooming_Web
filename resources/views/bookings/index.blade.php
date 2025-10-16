@extends('layouts.app')

@section('title', 'Bookings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4">Bookings</h1>
  @auth
    <a href="{{ route('bookings.create') }}" class="btn btn-primary">New Booking</a>
  @endauth
</div>

<div class="card">
  <div class="card-body p-0">
    @if($bookings->count() === 0)
      <p class="text-muted p-4 mb-0">No bookings found.</p>
    @else
      <div class="table-responsive">
        <table class="table mb-0">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Service</th>
              <th>Customer</th>
              <th>Staff</th>
              <th>Scheduled</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($bookings as $booking)
              <tr>
                <td>{{ $booking->id }}</td>
                <td>{{ $booking->service->name }}</td>
                <td>{{ $booking->customer->name }}</td>
                <td>{{ $booking->staff?->name ?? 'Unassigned' }}</td>
                <td>{{ $booking->scheduled_at->format('M j, Y g:i A') }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
  @if(method_exists($bookings, 'links') && $bookings->hasPages())
    <div class="card-footer">
      {{ $bookings->links() }}
    </div>
  @endif
</div>
@endsection
