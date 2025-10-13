@extends('layouts.app')

@section('title', 'Our Services')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Our Landscaping Services</h1>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.services.create') }}" class="btn btn-success">Add New Service</a>
                @endif
            @endauth
        </div>
        
        <div class="row">
            @forelse($services as $service)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($service->image_path)
                            <img src="{{ asset('storage/' . $service->image_path) }}" class="card-img-top" alt="{{ $service->name }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $service->name }}</h5>
                            <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                            <p class="text-success fw-bold">${{ number_format($service->price, 2) }}</p>
                            @if($service->duration)
                                <small class="text-muted">Duration: {{ $service->duration }} minutes</small>
                            @endif
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('services.show', $service) }}" class="btn btn-primary">View Details</a>
                            @auth
                                <a href="{{ route('bookings.create', ['service_id' => $service->id]) }}" class="btn btn-success">Book Now</a>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">No services available at the moment.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection