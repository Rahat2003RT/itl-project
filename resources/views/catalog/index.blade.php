@extends('layouts.main')

@section('title', 'Catalog')

@section('content')
<div class="container">
    <div class="row">
        <!-- Панель навигации -->
        <div class="col-md-3">
            <h4>Categories</h4>
            <ul class="list-group">
                <li class="list-group-item">
                    <a href="{{ route('catalog.index') }}">Все категории</a>
                </li>
                @foreach($categories as $category)
                    @include('partials.category', ['category' => $category])
                @endforeach
            </ul>

            <h4 class="mt-4">Filters</h4>
            <form action="{{ route('catalog.index', ['category_name' => $category_name]) }}" method="GET">
                @if ($brands->where('products_count', '>', 0)->count() > 0)
                    <div class="form-group">
                        <h5>Brands</h5>
                        <ul class="list-group">
                            @foreach($brands as $brand)
                                @if($brand->products_count > 0)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="brands[]" value="{{ $brand->id }}" class="form-check-input" {{ in_array($brand->id, $selectedBrands) ? 'checked' : '' }}>
                                            {{ $brand->name }} ({{ $brand->products_count }})
                                        </label>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
                <!-- Фильтр по атрибутам -->
                @if ($attributes->count() > 0)
                    <div class="form-group mt-3">
                        <ul class="list-group">
                            @foreach($attributes as $attribute)
                                <li class="list-group-item">
                                    <h6>{{ $attribute->name }}</h6>
                                    <ul class="list-unstyled">
                                        @foreach($attribute->values as $value)
                                            <li>
                                                <label class="form-check-label">
                                                    <input type="checkbox" name="attributes[{{ $attribute->id }}][]" value="{{ $value->value }}" class="form-check-input" {{ isset($selectedAttributes[$attribute->id]) && in_array($value->value, $selectedAttributes[$attribute->id]) ? 'checked' : '' }}>
                                                    {{ $value->value }}
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group mt-3">
                    <label for="min_price">Min Price</label>
                    <input type="number" name="min_price" id="min_price" class="form-control" value="{{ request('min_price', $minProductPrice) }}" min="{{ $minProductPrice }}" max="{{ $maxProductPrice }}">
                </div>
                <div class="form-group mt-3">
                    <label for="max_price">Max Price</label>
                    <input type="number" name="max_price" id="max_price" class="form-control" value="{{ request('max_price', $maxProductPrice) }}" min="{{ $minProductPrice }}" max="{{ $maxProductPrice }}">
                </div>
                <div class="form-group mt-3">
                    <label for="sort">Sort By</label>
                    <select name="sort" id="sort" class="form-control">
                        <option value="date" {{ request('sort') == 'date' ? 'selected' : '' }}>Date</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price (Low to High)</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price (High to Low)</option>
                        <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Popularity</option>
                    </select>
                </div>
                <div class="mt-3 d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('catalog.index') }}" class="btn btn-secondary">Clear Filters</a>
                </div>
            </form>
        </div>

        <!-- Товары -->
        <div class="col-md-9">
            <h4>Products</h4>
            <div class="row">
                @foreach($products as $product)
                    <div class="col-md-4">
                        <div class="card mb-4">
                        <img src="{{ $product->images->isNotEmpty() ? asset('storage/' . $product->images->first()->image_url) : asset('storage/default/default-product.png') }}" class="card-img-top img-fluid" alt="{{ $product->name }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text">{{ $product->price }}</p>
                                <a href="{{ route('catalog.product.show', $product->id) }}" class="btn btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Пагинация -->
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
