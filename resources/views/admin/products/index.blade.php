@extends('layouts.admin')

@section('title', 'Товары')

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

        .preview-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 10px;
            margin-bottom: 10px;
            transition: transform 0.3s ease-in-out;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
        }

        .preview-image:hover {
            transform: scale(1.2);
            z-index: 1;
        }

        .sortable-placeholder {
            border: 2px dashed #ccc;
            background: #f9f9f9;
            height: 50px;
            width: 50px;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .description-cell {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
<div class="container mt-5">
    <h1>Товары</h1>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Цена</th>
                <th>Категория</th>
                <th>Бренд</th>
                <th>Изображения</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td class="description-cell" title="{{ $product->description }}">{{ $product->description }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>{{ $product->brand ? $product->brand->name : '' }}</td>
                    <td>
                        @if ($product->images->isNotEmpty())
                            <div id="images-container">
                                @foreach ($product->images as $image)
                                    <img src="{{ asset('storage/' . $image->image_url) }}" data-image-id="{{ $image->order }}" alt="Изображение товара" class="preview-image">
                                @endforeach
                            </div>
                        @else
                            Нет изображений
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm">Редактировать</a>
                        <a href="{{ route('admin.products.manage', $product->id) }}" class="btn btn-primary btn-sm">Управление атрибутами</a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите удалить этот товар?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>

    <h2>Добавить новый товар</h2>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Название товара</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Описание товара</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Цена товара</label>
            <input type="number" class="form-control" id="price" name="price" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Категория</label>
            <select class="form-control" id="category" name="category" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="brand_id" class="form-label">Бренд</label>
            <select class="form-control" id="brand_id" name="brand_id">
                <option value="">Нет</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="images" class="form-label">Изображения товара</label>
            <input type="file" class="form-control" id="images" name="images[]" multiple>
        </div>
        <div class="mb-3">
            <p>Удерживайте и перетаскивайте для определения порядка:</p>
            <ul id="sortable" class="list-group d-flex flex-wrap flex-row"></ul>
            <input type="hidden" name="image_order" id="image_order">
        </div>
        <button type="submit" class="btn btn-primary">Добавить товар</button>
    </form>
</div>

<script>
document.getElementById('images').addEventListener('change', async function(event) {
    var files = event.target.files;
    var sortableList = document.getElementById('sortable');
    sortableList.innerHTML = ''; // Очищаем список перед добавлением новых элементов

    // Здесь будем сохранять порядок файлов
    var fileIndexes = [];

    // Функция для чтения файлов
    async function readFiles(index) {
        if (index >= files.length) {
            // Все файлы прочитаны, обновляем порядок и выходим
            updateImageOrder();
            return;
        }

        var file = files[index];
        var reader = new FileReader();

        reader.onload = function(e) {
            var listItem = document.createElement('li');
            listItem.classList.add('list-group-item', 'preview-image');
            listItem.style.backgroundImage = 'url(' + e.target.result + ')';
            listItem.style.backgroundSize = 'cover';
            listItem.style.backgroundPosition = 'center';
            listItem.setAttribute('data-file-index', fileIndexes[index]); // Устанавливаем индекс из сохранённого массива
            sortableList.appendChild(listItem);

            // Читаем следующий файл рекурсивно
            readFiles(index + 1);
        };

        // Читаем файл как data URL
        reader.readAsDataURL(file);

        // Сохраняем порядок файла
        fileIndexes.push(index);
    }

    // Начинаем чтение файлов с индекса 0
    readFiles(0);
});

    new Sortable(document.getElementById('sortable'), {
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: function(evt) {
            updateImageOrder(); // Обновляем порядок изображений после сортировки
        }
    });

    function updateImageOrder() {
        var order = [];
        $('#sortable li').each(function() {
            order.push($(this).data('file-index'));
        });
        $('#image_order').val(order.join(','));
    }

    // Вызываем updateImageOrder() сразу после загрузки страницы, чтобы установить начальный порядок
    $(document).ready(function() {
        updateImageOrder();
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Получаем контейнер с изображениями
    const imagesContainer = document.getElementById('images-container');
    
    if (imagesContainer) {
        // Получаем все изображения в контейнере
        const images = Array.from(imagesContainer.getElementsByTagName('img'));

        // Сортируем изображения по значению data-image-id
        images.sort((img1, img2) => {
            const order1 = parseInt(img1.getAttribute('data-image-id'));
            const order2 = parseInt(img2.getAttribute('data-image-id'));
            return order1 - order2;
        });

        // Очищаем контейнер и добавляем отсортированные изображения
        imagesContainer.innerHTML = '';
        images.forEach(img => imagesContainer.appendChild(img));
    } else {
        console.error('Контейнер с изображениями не найден.');
    }
});
</script>
@endsection
