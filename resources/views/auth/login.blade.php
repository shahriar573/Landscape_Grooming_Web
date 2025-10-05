@extends('layouts.app')
@section('content')
<h3>Login</h3>
<form method="POST" action="{{ route('login') }}">
  @csrf
  <input name="email_or_mobile" placeholder="Email or Mobile" class="form-control mb-2">
  <input name="password" type="password" placeholder="Password" class="form-control mb-2">
  <button class="btn btn-primary">Login</button>
</form>
@endsection
