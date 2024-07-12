@extends('layouts.main')

@section('title', 'Уведомления')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    Уведомления
                </div>
                <div class="card-body">
                    @if ($notifications->isEmpty())
                        <p>У вас пока нет уведомлений.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($notifications as $notification)
                                <li class="list-group-item">
                                    <strong>{{ $notification->created_at->format('d.m.Y H:i') }}</strong> - {!! $notification->data !!}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
