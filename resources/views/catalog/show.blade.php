@extends('layouts.main')

@section('title', $product->name)

@section('content')
<div class="container">
    <!-- Отображение пути категорий -->
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @foreach($categoryPath as $category)
                        <li class="breadcrumb-item">
                            <a href="{{ route('catalog.filter', ['category_name' => $category->name]) }}">{{ $category->name }}</a>
                        </li>
                    @endforeach
                    <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Верхний блок: информация о продукте -->
    <div class="row mb-4">
        <div class="col-md-6">
            <!-- Слайдер изображений -->
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @forelse($product->images as $image)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <img src="{{ asset('storage/' . $image->image_url) }}" class="d-block w-100 img-fluid" alt="{{ $product->name }}" data-bs-toggle="modal" data-bs-target="#imageModal">
                        </div>
                    @empty
                        <div class="carousel-item active">
                            <img src="{{ asset('storage/default/default-product.png') }}" class="d-block w-100 img-fluid" alt="Placeholder">
                        </div>
                    @endforelse
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Информация о продукте -->
            <h1>{{ $product->name }}</h1>

            <!-- Кнопка "Добавить в избранное" -->
            <div class="mb-3">
                @if ($product->isFavorite())
                    <form action="{{ route('product.favorite.remove', $product->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-heart"></i> Удалить из избранного
                        </button>
                    </form>
                @else
                    <form action="{{ route('product.favorite', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="far fa-heart"></i> Добавить в избранное
                        </button>
                    </form>
                @endif
            </div>

            <!-- Описание продукта -->
            @if($product->description)
                <p><strong>Описание:</strong> {{ $product->description }}</p>
            @endif

            <!-- Цена продукта -->
            <p><strong>Цена:</strong> {{ $product->price }} $</p>

            <!-- Бренд продукта -->
            @if($product->brand)
                <p><strong>Бренд:</strong> {{ $product->brand->name }}</p>
            @endif

            <!-- Рейтинг продукта -->
            @if($product->reviews->isNotEmpty())
                <p><strong>Средний рейтинг:</strong> {{ $product->averageRating() }}/5</p>
            @else
                <p><strong>Отзывов пока нет.</strong></p>
            @endif

            <!-- Кнопка "Добавить в корзину" -->
            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addToCartModal">
                Добавить в корзину
            </button>
        </div>

    </div>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="characteristics-tab" data-bs-toggle="tab" href="#characteristics" role="tab" aria-controls="characteristics" aria-selected="true">Характеристики</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="reviews-tab" data-bs-toggle="tab" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false">Отзывы</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="related-products-tab" data-bs-toggle="tab" href="#related-products" role="tab" aria-controls="related-products" aria-selected="false">Товары из коллекции</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <!-- Панель для характеристик -->
        <div class="tab-pane fade show active" id="characteristics" role="tabpanel" aria-labelledby="characteristics-tab">
            <!-- Ваш код для вывода характеристик здесь -->
            @include('partials.catalog.characteristics')
        </div>

        <!-- Панель для отзывов -->
        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
            <!-- Ваш код для вывода отзывов здесь -->
            @include('partials.catalog.reviews')
        </div>

        <!-- Панель для товаров из коллекции -->
        <div class="tab-pane fade" id="related-products" role="tabpanel" aria-labelledby="related-products-tab">
            <!-- Ваш код для вывода товаров из коллекции здесь -->
            @include('partials.catalog.related-products')
        </div>
    </div>
</div>

<!-- Модальное окно для добавления в корзину -->
<div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addToCartModalLabel">Add to Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity:</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1">
                    </div>
                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для увеличенного изображения -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body p-0">
                @if($product->images->isNotEmpty())
                    <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" class="img-fluid w-100" alt="{{ $product->name }}">
                @else
                    <img src="{{ asset('storage/default/default-product.png') }}" class="img-fluid w-100" alt="Placeholder">
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Переключение модального окна для изображения
    $('#imageModal').on('shown.bs.modal', function () {
        $(this).find('img').click(function () {
            $(this).toggleClass('img-fluid w-100');
        });
    });

    // Инициализация карусели и табов
    $(document).ready(function(){
        $('#productCarousel').carousel(); // Инициализация карусели

        $('#myTab a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#toggleFavorite').click(function() {
            $.ajax({
                url: "{{ route('products.toggleFavorite', $product->id) }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Обновляем иконку сердечка
                        $('#toggleFavorite i').toggleClass('bi-heart bi-heart-fill');
                    } else {
                        // Обработка ошибок, если необходимо
                        console.error('Ошибка при выполнении запроса');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>

@endsection