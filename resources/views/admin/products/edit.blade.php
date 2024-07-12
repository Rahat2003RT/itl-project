@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="edit-product-form">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="name" class="form-label">Product Name</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Product Description</label>
        <textarea class="form-control" id="description" name="description" required>{{ $product->description }}</textarea>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Product Price</label>
        <input type="number" class="form-control" id="price" name="price" value="{{ $product->price }}" required>
    </div>
    <div class="mb-3">
        <label for="category" class="form-label">Categories</label>
        <select class="form-control" id="category" name="category" required>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ $product->category->id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="brand_id" class="form-label">Brand</label>
        <select class="form-control" id="brand_id" name="brand_id">
            <option value="">None</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="images" class="form-label">Product Images</label>
        <input type="file" class="form-control" id="images" name="images[]" multiple>
    </div>
    <div class="mb-3">
        <p>Удерживай и перетаскивай для определения порядка. В случае добавления новых изображений старые удаляются:</p>
        <ul id="sortable" class="list-group d-flex flex-wrap flex-row">
            @foreach ($product->images as $image)
                <li class="list-group-item preview-image" style="background-image: url('{{ asset('storage/' . $image->image_url) }}'); background-size: cover; background-position: center center" data-file-index="{{ $image->order }}" data-file-id="{{ $image->id }}"></li>
            @endforeach
        </ul>
        <input type="hidden" name="image_order" id="image_order">
        <input type="hidden" name="image_id" id="image_id">
    </div>
    <button type="submit" class="btn btn-primary">Update Product</button>
</form> 
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Получаем контейнер с изображениями
    const sortableList = document.getElementById('sortable');
    
    if (sortableList) {
        // Получаем все изображения в контейнере
        const items = Array.from(sortableList.getElementsByTagName('li'));

        // Сортируем изображения по значению data-file-index
        items.sort((item1, item2) => {
            const order1 = parseInt(item1.getAttribute('data-file-index'));
            const order2 = parseInt(item2.getAttribute('data-file-index'));
            return order1 - order2;
        });

        // Очищаем контейнер и добавляем отсортированные изображения
        sortableList.innerHTML = '';
        items.forEach(item => sortableList.appendChild(item));
    } else {
        console.error('Sortable list container not found.');
    }
});

document.getElementById('images').addEventListener('change', function(event) {
    var files = event.target.files;
    var sortableList = document.getElementById('sortable');
    sortableList.innerHTML = '';
    var currentIndex = 0; // Начальный индекс для новых изображений

    Array.from(files).forEach((file, index) => {
        var reader = new FileReader();
        reader.onload = function(e) {
            var listItem = document.createElement('li');
            listItem.classList.add('list-group-item', 'preview-image');
            listItem.style.backgroundImage = 'url(' + e.target.result + ')';
            listItem.style.backgroundSize = 'cover';
            listItem.style.backgroundPosition = 'center';
            listItem.setAttribute('data-file-index', currentIndex); // Устанавливаем индекс для нового изображения
            sortableList.appendChild(listItem);

            currentIndex++;

            updateImageOrder();
        };
        reader.readAsDataURL(file);
    });
});



new Sortable(document.getElementById('sortable'), {
    animation: 150,
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    onEnd: function(evt) {
        updateImageOrder();
    }
});

function updateImageOrder() {
    var order = [];
    var ids = [];
    $('#sortable li').each(function(index) {
        order.push($(this).attr('data-file-index'));
        ids.push($(this).attr('data-file-id')); // Используем data-file-id
    });
    $('#image_order').val(order.join(','));
    $('#image_id').val(ids.join(','));
}


$(document).ready(function() {
    updateImageOrder();
});

</script>
@endsection
