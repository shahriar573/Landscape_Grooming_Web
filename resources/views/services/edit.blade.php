@extends('layouts.app')
@section('content')
<h3>Edit Service</h3>
<form method="POST" action="{{ route('services.update', $service) }}">
  @csrf
  @method('PUT')
  <input name="name" value="{{ $service->name }}" class="form-control mb-2">
  <textarea name="description" class="form-control mb-2">{{ $service->description }}</textarea>
  <input name="price" value="{{ $service->price }}" class="form-control mb-2">
  <input name="duration" value="{{ $service->duration }}" class="form-control mb-2">
  <button class="btn btn-primary">Update</button>
</form>
@endsection
