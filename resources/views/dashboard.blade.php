@extends('layouts.app')
@section('content')
<h3>Admin Dashboard</h3>
<div class="row">
  <div class="col-md-4">
    <div class="card p-3">
      <h5>Total Users</h5>
      <h2>{{ $totalUsers }}</h2>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
      <h5>Total Bookings</h5>
      <h2>{{ $totalBookings }}</h2>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
      <h5>Total Revenue</h5>
      <h2>৳{{ number_format($totalRevenue,2) }}</h2>
    </div>
  </div>
</div>
@endsection
