@extends('layouts.main')


@section('title', 'Catalog')

@section('content')
<div class="container">
    <div class="row">
        <!-- Панель навигации -->
        <div class="col-md-3">
            <h4>Categories</h4>
            <ul class="list-group">
                @foreach($categories as $parentCategory)
                    <li class="list-group-item">
                        <a href="{{ route('catalog.filter', ['category_id' => $parentCategory->id]) }}">{{ $parentCategory->name }}</a>
                        @if($parentCategory->children->isNotEmpty())
                            <ul class="list-group">
                                @foreach($parentCategory->children as $childCategory)
                                    <li class="list-group-item">
                                        <a href="{{ route('catalog.filter', ['category_id' => $childCategory->id]) }}">{{ $childCategory->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Товары и фильтры -->
        <div class="col-md-9">
            <h4>Filters</h4>
            <form action="{{ route('catalog.filter') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <label for="min_price">Min Price</label>
                        <input type="number" name="min_price" id="min_price" class="form-control" value="{{ request('min_price') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="max_price">Max Price</label>
                        <input type="number" name="max_price" id="max_price" class="form-control" value="{{ request('max_price') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="sort">Sort By</label>
                        <select name="sort" id="sort" class="form-control">
                            <option value="date" {{ request('sort') == 'date' ? 'selected' : '' }}>Date</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price (Low to High)</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price (High to Low)</option>
                            <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Popularity</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>

            <h4 class="mt-4">Products</h4>
            <div class="row">
                @foreach($products as $product)
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="{{ $product->images->isNotEmpty() ? asset('storage/' . $product->images->first()->image_url) : asset('path/to/default/image.jpg') }}" class="card-img-top" alt="{{ $product->name }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text">{{ $product->description }}</p>
                                <p class="card-text">{{ $product->price }} $</p>
                                <p class="card-text">Brand: {{ $product->brand ? $product->brand->name : 'No brand assigned' }}</p>
                                <a href="#" class="btn btn-primary">View Product</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection