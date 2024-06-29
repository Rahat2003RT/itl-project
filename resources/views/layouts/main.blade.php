<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ITL')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <style>
        .cart-dropdown .dropdown-menu {
            min-width: 300px; /* Увеличение ширины всплывающего окна корзины */
        }
        .cart-dropdown .dropdown-item img {
            width: 80px; /* Увеличение размера изображения */
            height: 80px;
            object-fit: cover;
            margin-right: 10px; /* Отступ между изображением и текстом */
        }
        .cart-dropdown .dropdown-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .cart-dropdown .dropdown-item .item-info {
            flex-grow: 1;
            margin-left: 10px;
        }
        .cart-dropdown .btn-sm {
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">Logo</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="{{ route('catalog.index') }}">Catalog</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard')}}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile') }}">{{ auth()->user()->name }}</a>
                            </li>
                            @if (auth()->check() && auth()->user()->role == 'admin')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard')}}">Admin</a>
                                </li>
                            @endif
                            @if (auth()->check() && (auth()->user()->role == 'admin' || auth()->user()->role == 'manager'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('manager.dashboard')}}">Manager</a>
                                </li>
                            @endif
                            <li class="nav-item dropdown">
                                <a class="nav-link" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bell"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                                    <li><a class="dropdown-item" href="#">Уведомлений пока нет</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown cart-dropdown">
                                <a class="nav-link" href="#" id="cartDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="badge bg-danger">{{ $cartItems->count() }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="cartDropdown">
                                    @if ($cartItems->isEmpty())
                                        <li><a class="dropdown-item" href="{{ route('cart.index')}}">Ваша корзина пуста</a></li>
                                    @else
                                        @foreach($cartItems as $item)
                                            <li class="dropdown-item">
                                                <div class="d-flex align-items-center">
                                                    @if ($item->product->images->first())
                                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_url) }}" alt="{{ $item->product->name }}" class="img-thumbnail">
                                                    @endif
                                                    <div class="item-info">
                                                        <span>{{ $item->product->name }}</span>
                                                        <span>x {{ $item->quantity }}</span>
                                                    </div>
                                                </div>
                                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </li>
                                        @endforeach
                                        <li><a class="dropdown-item text-center" href="{{ route('cart.index') }}">Перейти в корзину</a></li>
                                    @endif
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('logout')}}">
                                    <i class="fas fa-sign-out-alt"></i>
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Register</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('admin-nav')

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

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
