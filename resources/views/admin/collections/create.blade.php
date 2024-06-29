@extends('layouts.admin')

@section('title', 'Create Collection')

@section('content')
    <div class="container">
        <h1>Create New Collection</h1>

        <form action="{{ route('admin.collections.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
            </div>
            <button type="submit" class="btn btn-success">Create Collection</button>
        </form>
    </div>
@endsection
