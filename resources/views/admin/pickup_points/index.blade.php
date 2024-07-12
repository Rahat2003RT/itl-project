@extends('layouts.admin')

@section('title', 'Пункты выдачи')

@section('content')
    <h1>Пункты выдачи</h1>
    <a href="{{ route('admin.pickup-points.create') }}" class="btn btn-success mb-3">Создать новый пункт выдачи</a>

    @if($pickupPoints->isEmpty())
        <p>Нет доступных пунктов выдачи.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Адрес</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pickupPoints as $pickupPoint)
                    <tr>
                        <td>{{ $pickupPoint->name }}</td>
                        <td>{{ $pickupPoint->address }}</td>
                        <td>
                            <a href="{{ route('admin.pickup-points.edit', $pickupPoint->id) }}" class="btn btn-sm btn-primary">Редактировать</a>
                            <form action="{{ route('admin.pickup-points.destroy', $pickupPoint->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите удалить этот пункт выдачи?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
