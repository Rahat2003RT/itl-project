@extends('layouts.main')

@section('title', 'Смена имени')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Сменить имя</h1>


        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Имя</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Сохранить имя</button>
        </form>
    </div>
@endsection
