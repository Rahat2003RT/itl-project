@extends('layouts.admin')

@section('title', 'Все коллекции')

@section('content')
    <div class="container">
        <h1>Все коллекции</h1>

        <a href="{{ route('admin.collections.create') }}" class="btn btn-success mb-3">Создать новую коллекцию</a>

        <table class="table">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Создано пользователем</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($collections as $collection)
                    <tr>
                        <td>{{ $collection->name }}</td>
                        <td>{{ $collection->description }}</td>
                        <td>{{ $collection->user->name }}</td>
                        <td>
                            <a href="{{ route('admin.collections.show', $collection->id) }}" class="btn btn-info">Просмотр</a>
                            <a href="{{ route('admin.collections.edit', $collection->id) }}" class="btn btn-primary">Редактировать</a>
                            <a href="{{ route('admin.collections.manage', $collection->id) }}" class="btn btn-warning">Управление товарами</a>
                            <form action="{{ route('admin.collections.destroy', $collection->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены?')">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
