@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            <h1>Create Pickup Point</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pickup-points.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
@endsection
