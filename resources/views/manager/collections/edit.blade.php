@extends('layouts.manager')

@section('title', 'Редактирование коллекций')

@section('content')
    <div class="container">
        <h1>Редактирование коллекций: {{ $collection->name }}</h1>

        <form action="{{ route('manager.collections.update', $collection->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Имя</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $collection->name }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Описание</label>
                <textarea class="form-control" id="description" name="description">{{ $collection->description }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Обновить</button>
        </form>
    </div>
@endsection
