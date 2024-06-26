@extends('layouts.admin')

@section('title', 'Attributes')

@section('content')
    <div class="container">
        <h1>Attributes</h1>

        <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary mb-3">Add Attribute</a>

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
                @forelse ($attributes as $attribute)
                    <tr>
                        <td>{{ $attribute->id }}</td>
                        <td>{{ $attribute->name }}</td>
                        <td>{{ $attribute->category_id}}</td>
                        <td>{{ $attribute->created_at->format('d M Y H:i:s') }}</td>
                        <td>
                            <a href="{{ route('admin.attribute_values.index', $attribute->id) }}" class="btn btn-secondary btn-sm">Manage Values</a>
                            <!-- Добавьте другие действия, такие как редактирование и удаление, по необходимости -->
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No attributes found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
