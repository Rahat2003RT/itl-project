@extends('layouts.admin')

@section('title', 'Изменить категорию')

@section('content')
<div class="container">
    <h1>Изменить категорию</h1>

    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Название категории</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}" required>
        </div>
        <div class="mb-3">
            <label for="parent_id" class="form-label">Родительская категория (необязательно)</label>
            <select class="form-control" id="parent_id" name="parent_id">
                <option value="">Нет</option>
                @foreach ($categories as $parentCategory)
                    @if ($parentCategory->id != $category->id)
                        <option value="{{ $parentCategory->id }}" {{ $category->parent_id == $parentCategory->id ? 'selected' : '' }}>{{ $parentCategory->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
    </form>
</div>
@endsection
