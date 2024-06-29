@extends('layouts.main')

@section('title', 'Обновление адреса')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Адрес</h2>
            <form action="{{ route('addresses.update') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="address_line_1" class="form-label">Адресная строка 1:</label>
                    <input type="text" class="form-control" id="address_line_1" name="address_line_1" value="{{ isset($address) ? $address->address_line_1 : '' }}">
                </div>

                <div class="mb-3">
                    <label for="address_line_2" class="form-label">Адресная строка 2:</label>
                    <input type="text" class="form-control" id="address_line_2" name="address_line_2" value="{{ isset($address) ? $address->address_line_2 : '' }}">
                </div>

                <div class="mb-3">
                    <label for="city" class="form-label">Город:</label>
                    <input type="text" class="form-control" id="city" name="city" value="{{ isset($address) ? $address->city : '' }}">
                </div>

                <div class="mb-3">
                    <label for="state" class="form-label">Регион или область:</label>
                    <input type="text" class="form-control" id="state" name="state" value="{{ isset($address) ? $address->state : '' }}">
                </div>

                <div class="mb-3">
                    <label for="postal_code" class="form-label">Почтовый индекс:</label>
                    <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ isset($address) ? $address->postal_code : '' }}">
                </div>

                <div class="mb-3">
                    <label for="country" class="form-label">Страна:</label>
                    <input type="text" class="form-control" id="country" name="country" value="{{ isset($address) ? $address->country : '' }}">
                </div>

                <button type="submit" class="btn btn-primary">Сохранить адрес</button>
            </form>
        </div>
    </div>
</div>
@endsection
