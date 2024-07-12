@extends('layouts.admin')

@section('title', 'Add Attribute Value')

@section('content')
    <div class="container">
        <h2>Характеристика: {{ $attribute->name }}</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Создано</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attribute_values as $attribute_value)
                    <tr>
                        <td>{{ $attribute_value->id }}</td>
                        <td>{{ $attribute_value->value }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $attribute_value->created_at->format('d M Y H:i:s') }}</td>
                        <td>
                            <a href="#" class="btn btn-primary btn-sm edit-btn" data-id="{{ $attribute_value->id }}" data-value="{{ $attribute_value->value }}">Редактировать</a>
                            <a href="{{ route('admin.attribute_values.destroy', ['attribute_value' => $attribute_value->id]) }}" class="btn btn-danger btn-sm"
                               onclick="event.preventDefault(); if(confirm('Вы уверены, что хотите удалить это значение атрибута?')) { document.getElementById('delete-form-{{ $attribute_value->id }}').submit(); }">
                                Удалить
                            </a>
                            <form id="delete-form-{{ $attribute_value->id }}" action="{{ route('admin.attribute_values.destroy', ['attribute_value' => $attribute_value->id]) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Значений не найдено.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h1>Добавить значение</h1>

        <div class="mb-3">
            <form action="{{ route('admin.attribute_values.store', $attribute->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="value" class="form-label">Новое значение</label>
                    <input type="text" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value') }}" required>
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Добавить</button>
            </form>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Редактировать значение атрибута</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm" action="" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="editValue" class="form-label">Новое значение</label>
                                <input type="text" class="form-control" id="editValue" name="value" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.edit-btn');
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        const editForm = document.getElementById('editForm');
        const editValueInput = document.getElementById('editValue');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const attributeValueId = this.getAttribute('data-id');
                const attributeValue = this.getAttribute('data-value');

                editForm.action = `/admin/attribute_values/${attributeValueId}/update`;
                editValueInput.value = attributeValue;

                editModal.show();
            });
        });
    });
</script>
@endsection
