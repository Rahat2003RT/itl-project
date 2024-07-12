@extends('layouts.manager')

@section('title', 'Просмотр коллекций')

@section('content')
    <div class="container">
        <h1>{{ $collection->name }}</h1>
        <p>{{ $collection->description }}</p>

        <h2>Товары коллекций :</h2>

        @if ($products->isEmpty())
            <p>В этой коллекции нет товаров.</p>
        @else
            <div class="row">
                @foreach ($products as $product)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            @if ($product->images->first())
                                <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" class="card-img-top" alt="{{ $product->name }}">
                            @else
                                <img src="{{ asset('storage/default/default-product.png') }}" class="card-img-top" alt="{{ $product->name }}">
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

        <a href="{{ route('manager.collections.index') }}" class="btn btn-warning">Назад к коллекциям</a>
    </div>
@endsection
