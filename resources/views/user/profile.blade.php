@extends('layouts.main')


@section('title', 'Home page')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Profile</h1>
        <div class="card">
            <div class="card-body">
                <p class="card-text"><strong>Name:</strong> {{ $user->name }}</p>
                <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
            </div>
        </div>
    </div>
@endsection