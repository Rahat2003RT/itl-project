@extends('layouts.admin')

@section('title', 'Add Attribute Value')

@section('content')
    <div class="container">


        <h2>Параметр: {{ $attribute->name }}</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attribute_values as $attribute_value)
                    <tr>
                        <td>{{ $attribute_value->id }}</td>
                        <td>{{ $attribute_value->value }}</td>
                        <td>{{ $category->name}}</td>
                        <td>{{ $attribute_value->created_at->format('d M Y H:i:s') }}</td>
                        <td>
                        <a href="{{ route('admin.attribute_values.destroy', ['attribute_value' => $attribute_value->id]) }}" class="btn btn-danger btn-sm"
                        onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this attribute value?')) { document.getElementById('delete-form-{{ $attribute_value->id }}').submit(); }">
                            Delete
                        </a>
                        <form id="delete-form-{{ $attribute_value->id }}" action="{{ route('admin.attribute_values.destroy', ['attribute_value' => $attribute_value->id]) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>

                        <a href="{{ route('admin.attribute_values.edit', ['attribute_value' => $attribute_value->id]) }}" class="btn btn-primary btn-sm">Edit</a>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No attributes found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <h1>Добавить значение</h1>

        <div class="mb-3">
            <form action="{{ route('admin.attribute_values.store', $attribute->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="value" class="form-label">Новое значение</label>
                    <input type="text" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value') }}" required>
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Добавить</button>
            </form>
        </div>
    </div>
@endsection
