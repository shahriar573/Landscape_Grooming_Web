@extends('layouts.app')
@section('content')
<h3>Create Booking</h3>
<form method="POST" action="{{ route('bookings.store') }}">
  @csrf
  <label>Service</label>
  <select name="service_id" class="form-control mb-2">
    @foreach($services as $s)
      <option value="{{ $s->id }}" {{ isset($prefill) && $prefill == $s->id ? 'selected' : '' }}>{{ $s->name }} (৳{{ number_format($s->price,2) }})</option>
    @endforeach
  </select>

  <label>Scheduled At</label>
  <input name="scheduled_at" type="datetime-local" class="form-control mb-2">

  <label>Notes</label>
  <textarea name="notes" class="form-control mb-2"></textarea>

  <button class="btn btn-primary">Create Booking</button>
</form>
@endsection
