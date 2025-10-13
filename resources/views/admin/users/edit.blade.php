@extends('layouts.app')
@section('content')
<h3>Edit User</h3>
<form method="POST" action="{{ route('admin.users.update', $user) }}">
  @csrf
  @method('PUT')
  <input name="name" value="{{ $user->name }}" class="form-control mb-2">
  <input name="email" value="{{ $user->email }}" class="form-control mb-2">
  <input name="mobile" value="{{ $user->mobile }}" class="form-control mb-2">
  <label>Role</label>
  <select name="role" class="form-control mb-2">
    <option value="customer" {{ $user->role=='customer' ? 'selected' : '' }}>Customer</option>
    <option value="staff" {{ $user->role=='staff' ? 'selected' : '' }}>Staff</option>
    <option value="admin" {{ $user->role=='admin' ? 'selected' : '' }}>Admin</option>
  </select>
  <button class="btn btn-primary">Save</button>
</form>
@endsection
