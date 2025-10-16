@extends('layouts.app')

@section('title', 'Create Booking')

@section('content')
<div class="card">
  <div class="card-header">
    <h1 class="h5 mb-0">Create Booking</h1>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('bookings.store') }}">
      @csrf

      <div class="mb-3">
        <label for="service_id" class="form-label">Service</label>
        <select id="service_id" name="service_id" class="form-select">
          @foreach($services as $service)
            <option value="{{ $service->id }}" {{ isset($prefill) && (int) $prefill === $service->id ? 'selected' : '' }}>
              {{ $service->name }} ({{ number_format($service->price, 2) }})
            </option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label for="scheduled_at" class="form-label">Scheduled At</label>
        <input id="scheduled_at" name="scheduled_at" type="datetime-local" class="form-control">
      </div>

      <div class="mb-3">
        <label for="notes" class="form-label">Notes</label>
        <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
      </div>

      <button class="btn btn-primary">Create Booking</button>
    </form>
  </div>
</div>
@endsection
