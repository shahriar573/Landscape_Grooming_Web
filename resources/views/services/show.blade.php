@extends('layouts.app')
@section('content')
<h3>{{ $service->name }}</h3>
<p>{{ $service->description }}</p>
<p>৳{{ number_format($service->price,2) }}</p>
<a href="{{ route('bookings.create') }}?service_id={{ $service->id }}" class="btn btn-success">Book Now</a>
@endsection
