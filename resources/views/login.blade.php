@extends('backend.app')
@section('title', 'Login')
@section('css')
<link rel="stylesheet" href="{{ asset('backend/css/auth.css') }}" />
@endsection
@section('authentication')
<div class="login-box">
        <h3 class="text-center mb-4">Admin Login</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Please fix the following issues:
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required autofocus value="{{ old('email') }}">
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-gradient-primary">Login</button>
            </div>
        </form>
    </div>
@endsection
