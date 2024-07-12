@extends('layouts.admin')

@section('title', 'Редактировать атрибут')

@section('content')
    <div class="container">
        <h1>Редактировать атрибут</h1>

        <form action="{{ route('admin.attributes.update', $attribute->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Название атрибута</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $attribute->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Тип атрибута</label>
                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                    <option value="Общая характеристика" {{ old('type', $attribute->type) == 'Общая характеристика' ? 'selected' : '' }}>Общая характеристика</option>
                    <option value="Техническая характеристика" {{ old('type', $attribute->type) == 'Техническая характеристика' ? 'selected' : '' }}>Техническая характеристика</option>
                    <option value="Дополнительная характеристика" {{ old('type', $attribute->type) == 'Дополнительная характеристика' ? 'selected' : '' }}>Дополнительная характеристика</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Категория</label>
                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                    <option value="">Категория не выбрана</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category', $attribute->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        </form>
    </div>
@endsection
