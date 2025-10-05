@extends('layouts.app')
@section('content')
<h3>Register</h3>
<form method="POST" action="{{ route('register') }}">
  @csrf
  <input name="name" placeholder="Name" class="form-control mb-2">
  <input name="email" placeholder="Email" class="form-control mb-2">
  <input name="mobile" placeholder="Mobile" class="form-control mb-2">
  <input name="password" type="password" placeholder="Password" class="form-control mb-2">
  <input name="password_confirmation" type="password" placeholder="Confirm Password" class="form-control mb-2">
  <button class="btn btn-primary">Register</button>
</form>
@endsection
