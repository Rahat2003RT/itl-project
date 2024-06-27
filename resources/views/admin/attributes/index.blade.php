@extends('layouts.admin')

@section('title', 'Attributes')

@section('content')
    <div class="container">
        <h1>Атрибуты</h1>

        <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary mb-3">Добавить атрибут</a>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Тип</th>
                    <th>Создано</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attributes as $attribute)
                    <tr>
                        <td>{{ $attribute->id }}</td>
                        <td>{{ $attribute->name }}</td>
                        <td>{{ $attribute->category->name}}</td>
                        <td>{{ $attribute->type}}</td>
                        <td>{{ $attribute->created_at->format('d M Y H:i:s') }}</td>
                        <td>
                            <a href="{{ route('admin.attribute_values.index', $attribute->id) }}" class="btn btn-secondary btn-sm">Управление значениями</a>
                            <a href="{{ route('admin.attributes.edit', $attribute->id) }}" class="btn btn-primary btn-sm">Редактировать</a>
                            <a href="{{ route('admin.attributes.destroy', $attribute->id) }}" class="btn btn-danger btn-sm"
                            onclick="event.preventDefault(); if (confirm('Вы уверены, что хотите удалить этот атрибут?')) { document.getElementById('delete-form-{{ $attribute->id }}').submit(); }">
                            Удалить
                            </a>

                            <form id="delete-form-{{ $attribute->id }}" action="{{ route('admin.attributes.destroy', $attribute->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
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
