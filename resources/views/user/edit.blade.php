@extends('layouts.main')

@section('title', 'Edit Profile')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Edit Profile</h1>


        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="delivery_address" class="form-label">Delivery Address</label>
                <input type="text" class="form-control" id="delivery_address" name="delivery_address" value="{{ old('delivery_address', $user->delivery_address) }}">
                @error('delivery_address')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
@endsection
