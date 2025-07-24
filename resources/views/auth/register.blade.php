@extends('layouts.app')

@section('content')
    <h2>Register</h2>

    @if($errors->any())
        <p>{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="/register">
        @csrf
        <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" required>
        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
    </form>

    <a href="/login">Login</a>
@endsection
