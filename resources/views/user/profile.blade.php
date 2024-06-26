@extends('layouts.main')

@section('title', 'Home page')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Profile</h1>
        <div class="card">
            <div class="card-body">
                <p class="card-text"><strong>Name:</strong> {{ $user->name }}</p>
                <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
                <p class="card-text"><strong>Delivery Address:</strong> {{ $user->delivery_address }}</p>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
        </div>
    </div>
@endsection
