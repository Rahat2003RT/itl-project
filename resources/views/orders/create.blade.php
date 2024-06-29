@extends('layouts.main')

@section('title', 'Оформление заказа')

@section('content')
    <div class="container mt-5">
        <h1>Оформление заказа</h1>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('orders.store') }}" method="POST">
                    @csrf

                    <!-- Здесь можно добавить поля для выбора товаров, количества и других необходимых данных -->

                    <button type="submit" class="btn btn-primary">Оформить заказ</button>
                </form>
            </div>
        </div>
    </div>
@endsection
    