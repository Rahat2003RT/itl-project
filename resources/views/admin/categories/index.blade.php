@extends('layouts.admin')

@section('title', 'Категорий')

@section('content')
<div class="container">
    <h1>Категорий</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-success mb-3">Добавить новую категорию</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>ID родителя</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->parent_id }}</td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary btn-sm">Редактировать</a>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите удалить эту категорию??');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Категорий пока нет.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
