@extends('layouts.admin')

@section('title', 'Управление товарами коллекции')

@section('content')
    <div class="container">
        <h1>Управление товарами для коллекции: {{ $collection->name }}</h1>

        <form action="{{ route('admin.collections.manageUpdate', $collection->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="products" class="form-label">Товары</label>
                <select multiple class="form-control custom-select" id="products" name="products[]">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ in_array($product->id, $collection->products->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Обновить</button>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#products').select2({
                placeholder: 'Выберите товары',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--multiple {
            height: auto;
            min-height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff;
            border-color: #007bff;
            padding: 0 10px;
            color: #fff;
            border-radius: 0.2rem;
            margin-top: 5px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
            cursor: pointer;
            margin-right: 5px;
        }
    </style>
@endpush
