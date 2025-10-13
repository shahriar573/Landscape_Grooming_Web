@extends('layouts.app')
@section('content')
<h3>Create Service</h3>
<form method="POST" action="{{ route('services.store') }}">
  @csrf
  <input name="name" placeholder="Name" class="form-control mb-2">
  <textarea name="description" placeholder="Description" class="form-control mb-2"></textarea>
  <input name="price" placeholder="Price" class="form-control mb-2">
  <input name="duration" placeholder="Duration (minutes)" class="form-control mb-2">
  <button class="btn btn-primary">Create</button>
</form>
@endsection
