@extends('layouts.main')


@section('title', 'Home page')

@section('content')
    <h1 class="h2">Login form</h1>

    <form action="{{ route('login.auth') }}" method="post">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">
                Email
            </label>
            <input name="email" type="email" class="form-control" id="email" placeholder="email">
        </div>


        <div class="mb-3">
            <label for="password" class="form-label">
                Password
            </label>
            <input name="password" type="password" class="form-control" id="password" placeholder="Password">
        </div>

        <div class="mb-3 form-check">
            <input name="remember" type="checkbox" class="form-check-input" id="remember">
            <label for="remember" class="form-check-label">
                Remember me
            </label>
        </div>
        
        <button type="submit" class="btn btn-primary">Login</button>

    </form>
@endsection