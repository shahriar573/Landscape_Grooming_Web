@extends('layouts.app')
@section('content')
<h3>Edit Booking #{{ $booking->id }}</h3>
<form method="POST" action="{{ route('bookings.update', $booking) }}">
  @csrf
  @method('PUT')
  <label>Service</label>
  <select name="service_id" class="form-control mb-2">
    @foreach(App\Models\Service::all() as $s)
      <option value="{{ $s->id }}" {{ $booking->service_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
    @endforeach
  </select>

  <label>Scheduled At</label>
  <input name="scheduled_at" type="datetime-local" value="{{ $booking->scheduled_at->format('Y-m-d\TH:i') }}" class="form-control mb-2">

  <label>Staff</label>
  <select name="staff_id" class="form-control mb-2">
    <option value="">-- Unassigned --</option>
    @foreach(App\Models\User::where('role','staff')->get() as $u)
      <option value="{{ $u->id }}" {{ $booking->staff_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
    @endforeach
  </select>

  <label>Notes</label>
  <textarea name="notes" class="form-control mb-2">{{ $booking->notes }}</textarea>

  <label>Status</label>
  <select name="status" class="form-control mb-2">
    @foreach(['pending','confirmed','in_progress','completed','cancelled'] as $s)
      <option value="{{ $s }}" {{ $booking->status == $s ? 'selected' : '' }}>{{ $s }}</option>
    @endforeach
  </select>

  <button class="btn btn-primary">Update Booking</button>
</form>
@endsection
