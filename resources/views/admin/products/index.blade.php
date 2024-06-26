@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="container mt-5">
    <h1>Products</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Images</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description }}</td>
                    <td>{{ $product->price }}</td>
                    <td>
                        @foreach ($product->categories as $category)
                            {{ $category->name }}@if (!$loop->last), @endif
                        @endforeach
                    </td>
                    <td>{{ $product->brand ? $product->brand->name : '' }}</td>
                    <td>
                        
                    @if ($product->images->isNotEmpty())
                        <div id="images-container">
                            @foreach ($product->images as $image)
                                <img src="{{ asset('storage/' . $image->image_url) }}" data-image-id="{{ $image->order }}" alt="Product Image" width="50" class="img-thumbnail">
                            @endforeach
                        </div>
                    @else
                        No Images
                    @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm">Edit</a>

                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>

    <h2>Add New Product</h2>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Product Description</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Product Price</label>
            <input type="number" class="form-control" id="price" name="price" required>
        </div>
        <div class="mb-3">
            <label for="categories" class="form-label">Categories</label>
            <select class="form-control" id="categories" name="categories[]" multiple required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="brand_id" class="form-label">Brand</label>
            <select class="form-control" id="brand_id" name="brand_id">
                <option value="">None</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="images" class="form-label">Product Images</label>
            <input type="file" class="form-control" id="images" name="images[]" multiple>
        </div>
        <div class="mb-3">
            <p>Удерживай и перетаскивай для определения порядка:</p>
            <ul id="sortable" class="list-group d-flex flex-wrap flex-row"></ul>
            <input type="hidden" name="image_order" id="image_order">
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
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
        console.error('Images container not found.');
    }
});
</script>

@endsection
