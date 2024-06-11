<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ITL')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar" style="background-color: #e3f2fd">
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
                    <a class="nav-link" href="{{ route('admin.users') }}">Пользователи</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="#">Категорий</a>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>