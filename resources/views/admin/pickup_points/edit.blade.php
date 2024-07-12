@extends('layouts.admin')

@section('title', 'Редактировать пункт выдачи')

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Редактировать пункт выдачи</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pickup-points.update', $pickupPoint->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Название</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $pickupPoint->name }}" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Адрес</label>
                    <input type="text" name="address" id="address" class="form-control" value="{{ $pickupPoint->address }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Обновить</button>
                <a href="{{ route('admin.pickup-points.index') }}" class="btn btn-secondary">Отмена</a>
            </form>
        </div>
    </div>
@endsection
