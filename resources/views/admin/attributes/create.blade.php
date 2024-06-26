@extends('layouts.admin')

@section('title', 'Добавить атрибут')

@section('content')
    <div class="container">
        <h1>Добавить атрибут</h1>

        <form action="{{ route('admin.attributes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Название атрибута</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Категория</label>
                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                    <option value="">Выберите категорию</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Отправить</button>
        </form>
    </div>
@endsection
