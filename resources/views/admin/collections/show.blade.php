@extends('layouts.admin')

@section('title', 'View Collection')

@section('content')
    <div class="container">
        <h1>{{ $collection->name }}</h1>
        <p>{{ $collection->description }}</p>

        <h2>Products in this Collection</h2>

        @if ($products->isEmpty())
            <p>No products in this collection.</p>
        @else
            <div class="row">
                @foreach ($products as $product)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            @if ($product->images->first())
                                <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" class="card-img-top" alt="{{ $product->name }}">
                            @else
                                <img src="https://via.placeholder.com/150" class="card-img-top" alt="{{ $product->name }}">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text">{{ $product->description }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <a href="{{ route('admin.collections.index') }}" class="btn btn-primary">Back to Collections</a>
    </div>
@endsection
