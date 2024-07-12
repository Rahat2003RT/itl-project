@extends('layouts.admin')

@section('title', 'Добавить категорию')

@section('content')
<div class="container">
    <h1>Добавить категорию</h1>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Название категории</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="parent_id" class="form-label">Родительская категория (необязательно)</label>
            <select class="form-control" id="parent_id" name="parent_id">
                <option value="">Нет</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Добавить категорию</button>
    </form>
</div>
@endsection
