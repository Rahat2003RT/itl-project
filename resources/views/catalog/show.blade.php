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
            <p>{{ $product->description }}</p>
            <p>{{ $product->price }} $</p>
            <p>Brand: {{ $product->brand ? $product->brand->name : 'No brand assigned' }}</p>
            @if($product->reviews->isNotEmpty())
                <p>Average Rating: {{ $product->averageRating() }}/5</p>
            @else
                <p>No reviews yet.</p>
            @endif
            <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addToCartModal">
                Add to Cart
            </button>
            <a href="{{ route('catalog.filter', ['category_name' => $product->categories->first()->name]) }}" class="btn btn-secondary mt-2">Back to Catalog</a>
        </div>
    </div>

    <!-- Нижний блок: комментарии -->
    <div class="row">
        <div class="col-md-12">
            <h2>Reviews</h2>
            @foreach($product->reviews as $review)
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">{{ $review->user->name }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Rating: {{ $review->rating }}/5</h6>
                        <p class="card-text">{{ $review->comment }}</p>
                        <p class="card-text"><small class="text-muted">{{ $review->created_at->format('d M Y') }}</small></p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Форма для написания комментария -->
    <div class="row mt-4">
        <div class="col-md-12">
            <h2>Add a Review</h2>
            <form action="{{ route('catalog.product.review', $product->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="rating">Rating</label>
                    <select name="rating" id="rating" class="form-control" required>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Good</option>
                        <option value="3">3 - Average</option>
                        <option value="2">2 - Poor</option>
                        <option value="1">1 - Terrible</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Comment</label>
                    <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
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
</script>
@endsection
