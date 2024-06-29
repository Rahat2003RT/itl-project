<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ITL')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .sortable-placeholder {
            border: 1px dashed #ccc;
            background: #f0f0f0;
            height: 100px;
        }
    </style>
    
    <style>
        .preview-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 10px;
            margin-bottom: 10px;
            transition: transform 0.3s ease-in-out;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
        }

        .preview-image:hover {
            transform: scale(1.2);
            z-index: 1;
        }

        .sortable-placeholder {
            border: 2px dashed #ccc;
            background: #f9f9f9;
            height: 100px;
            width: 100px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
    </style>

    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">AdminPanel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Вернуться на сайт</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.users.index') }}">Пользователи</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.categories.index') }}">Категории</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.attributes.index') }}">Категории и их связи</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.brands.index') }}">Бренды</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.pickup-points.index') }}">Пункты выдачи</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.products.index') }}">Товары</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.collections.index') }}">Коллекции</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="main my-3">
        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{session('success')}}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @yield('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.10.2/Sortable.min.js"></script>
</body>
</html>
