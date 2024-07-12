@extends('layouts.main')

@section('title', 'Корзина')

@section('content')
    <div class="container mt-5">
        <h1>Корзина</h1>
        @if($cartItems->isEmpty())
            <p>Ваша корзина пуста.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Продукт</th>
                        <th>Количество</th>
                        <th>Цена</th>
                        <th>Действие</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->product->price * $item->quantity }}</td>
                            <td>
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                    <button type="submit" class="btn btn-danger">Убрать</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                @if(!$user->address)
                    <p class="alert alert-warning">У вас не указан адрес доставки. Пожалуйста, добавьте адрес в вашем профиле.</p>
                @else
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                        Оформить заказ
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Модальное окно -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutModalLabel">Оформление заказа</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="pickup_point">Выберите пункт выдачи</label>
                            <select class="form-control" id="pickup_point" name="pickup_point" required>
                                @foreach($pickupPoints as $point)
                                    <option value="{{ $point->id }}">{{ $point->name }} - {{ $point->address }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <label for="payment_method">Способ оплаты</label>
                            <select class="form-control" id="payment_method" name="payment_method" required>
                                <option value="credit_card">Кредитная карта</option>
                                <option value="cash">Наличные</option>
                                @foreach($userCards as $card)
                                    <option value="{{ $card->id }}">Карта: **** **** **** {{ substr($card->card_number, -4) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Подтвердить заказ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#checkoutModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var modal = $(this);
                // You can perform additional actions here if necessary
            });
        });
    </script>
@endpush
