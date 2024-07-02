@extends('layouts.main')

@section('title', 'Профиль')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Профиль</h1>
        <div class="card mb-4">
            <div class="card-body">
                <p class="card-text"><strong>Имя:</strong> {{ $user->name }}</p>
                <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
                @if($user->address)
                    <p class="card-text"><strong>Адрес:</strong> {{ $user->address->address_line_1 }}</p>
                    <p class="card-text"><strong>Город:</strong> {{ $user->address->city }}</p>
                    <p class="card-text"><strong>Регион или область:</strong> {{ $user->address->state }}</p>
                    <p class="card-text"><strong>Почтовый индекс:</strong> {{ $user->address->postal_code }}</p>
                    <p class="card-text"><strong>Страна:</strong> {{ $user->address->country }}</p>
                @else
                    <p class="card-text"><em>Адрес не указан</em></p>
                @endif
                <a href="{{ route('profile.edit') }}" class="btn btn-success mb-3">Сменить имя</a>
                <br>
                <a href="{{ route('addresses.edit') }}" class="btn btn-success mb-3">Сменить адресс</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h2>Карты</h2>
                @if($cards->isEmpty())
                    <p class="card-text">Нет добавленных карт.</p>
                @else
                    <ul class="list-group">
                        @foreach($cards as $card)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Номер карты:</strong> {{ $card->card_number }}<br>
                                    <strong>Владелец карты:</strong> {{ $card->card_holder }}<br>
                                    <strong>Дата истечения:</strong> {{ $card->expiry_date }}
                                </div>
                                <form action="{{ route('profile.cards.destroy', $card->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Удалить</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <button type="button" class="btn btn-primary mt-4" data-bs-toggle="modal" data-bs-target="#addCardModal">
                    Добавить карту
                </button>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h2>Заказанные товары</h2>
                @if($user->orders->isEmpty())
                    <p class="card-text">Вы еще не делали заказов.</p>
                @else
                    @foreach($user->orders as $order)
                        <div class="mb-4">
                            <p><strong>Дата заказа:</strong> {{ $order->created_at }}</p>
                            <ul class="list-group">
                                @foreach($order->orderItems as $item)
                                    <li class="list-group-item">
                                        <strong>Продукт:</strong> {{ $item->product->name }}<br>
                                        <strong>Количество:</strong> {{ $item->quantity }}<br>
                                        <strong>Цена за единицу:</strong> {{ $item->price }}<br>
                                        <strong>Общая стоимость:</strong> {{ $item->quantity * $item->price }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Модальное окно для добавления карты -->
    <div class="modal fade" id="addCardModal" tabindex="-1" aria-labelledby="addCardModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCardModalLabel">Добавить карту</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('profile.cards.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="card_number" class="form-label">Номер карты</label>
                            <input type="text" class="form-control" id="card_number" name="card_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="card_holder" class="form-label">Владелец карты</label>
                            <input type="text" class="form-control" id="card_holder" name="card_holder" required>
                        </div>
                        <div class="mb-3">
                            <label for="expiry_date" class="form-label">Дата истечения (MM/YY)</label>
                            <input type="text" class="form-control" id="expiry_date" name="expiry_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" id="cvv" name="cvv" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Добавить карту</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
