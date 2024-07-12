@extends('layouts.main')

@section('title', 'Избранное')

@section('content')
<div class="container">
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
</div>
@endsection
