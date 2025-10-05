@extends('layouts.app')
@section('content')
<h3>Bookings</h3>
<table class="table">
<thead><tr><th>ID</th><th>Service</th><th>Customer</th><th>Staff</th><th>Scheduled</th><th>Status</th></tr></thead>
<tbody>
@foreach($bookings as $b)
<tr>
  <td>{{ $b->id }}</td>
  <td>{{ $b->service->name }}</td>
  <td>{{ $b->customer->name }}</td>
  <td>{{ $b->staff?->name ?? '-' }}</td>
  <td>{{ $b->scheduled_at }}</td>
  <td>{{ $b->status }}</td>
</tr>
@endforeach
</tbody>
</table>
@endsection
