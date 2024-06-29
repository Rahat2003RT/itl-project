@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Pickup Points</h1>
        <a href="{{ route('admin.pickup-points.create') }}" class="btn btn-primary">Create New Pickup Point</a>
    </div>
    
    @if($pickupPoints->isEmpty())
        <p>No pickup points available.</p>
    @else
        <div class="list-group">
            @foreach ($pickupPoints as $pickupPoint)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h5>{{ $pickupPoint->name }}</h5>
                        <p>{{ $pickupPoint->address }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.pickup-points.edit', $pickupPoint->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <form action="{{ route('admin.pickup-points.destroy', $pickupPoint->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
