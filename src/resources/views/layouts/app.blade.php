<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rese</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap" rel="stylesheet">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM"
        crossorigin="anonymous"
    />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>

<body class="bg-eee font-geist">
    <header class="container d-md-flex justify-content-between align-items-center py-md-4 py-2">
        <nav class="navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="navbar-toggler border-0 rounded-1 shadow-right-bottom bg-primary hamburger-menu" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
                    <span class="d-block w-50 bg-white hamburger-menu-line"></span>
                    <span class="d-block w-100 bg-white hamburger-menu-line"></span>
                    <span class="d-block w-25 bg-white hamburger-menu-line"></span>
                </button>
                <h1 class="d-inline-block fw-extrabold text-primary m-0 fs-3 fw-bold">Rese</h1>
            </div>
        </nav>
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu">
            <div class="offcanvas-header">
                <button type="button" class="btn btn-primary border-0 shadow-right-bottom custom-close-btn" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
            @php
                $isLoggedIn = Auth::guard('web')->check() || Auth::guard('owner')->check() || Auth::guard('admin')->check();
            @endphp
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link text-primary fw-medium text-center pt-0" href="/">Home</a></li>
                    @if ($isLoggedIn)
                        <li class="nav-item d-flex justify-content-center">
                            <form action="/logout" method="post" class="d-inline-block">
                                @csrf
                                <button type="submit" class="nav-link border-0 text-primary fw-medium pt-0">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link text-primary fw-medium text-center pt-0" href="{{ route('register') }}">Registration</a></li>
                        <li class="nav-item"><a class="nav-link text-primary fw-medium text-center pt-0" href="{{ route('login') }}">Login</a></li>
                    @endif
                    @if (Auth::guard('web')->check())
                        <li class="nav-item"><a class="nav-link text-primary fw-medium text-center pt-0" href="/mypage">MyPage</a></li>
                    @endif
                    @if (Auth::guard('owner')->check())
                        <li class="nav-item"><a class="nav-link text-primary fw-medium text-center pt-0" href="/owner">OwnerPage</a></li>
                    @endif
                    @if (Auth::guard('admin')->check())
                        <li class="nav-item"><a class="nav-link text-primary fw-medium text-center pt-0" href="/admin">AdminPage</a></li>
                    @endif
                </ul>
            </div>
        </div>
        @if (request()->routeIs('index', 'search'))
        <div class="shadow-right-bottom rounded-1 col-lg-6 col-md-7 col-12 bg-white">
            <form class="d-flex" action="/search" method="get">
                <div class="custom-dropdown col-sm-2 col-3 border-start rounded-start-1">
                    <div class="custom-btn px-2 h-100" id="areaDropdownBtn">
                        <p class="fs-0_98vw fw-bold m-0 select__placeholder">All area</p>
                        <svg class="arrow" viewBox="0 0 12 16">
                            <path d="M6 10 L12 7 L6 16 L0 8 Z" fill="#d6dfff"/>
                        </svg>
                    </div>
                    <ul class="custom-menu">
                        @foreach ($areas as $area)
                        <li data-value="{{ $area->id }}">{{ $area->name }}</li>
                        @endforeach
                    </ul>
                    <input type="hidden" name="area_id" id="areaInput">
                </div>
                <div class="custom-dropdown col-sm-2 col-3 my-1">
                    <div class="custom-btn-genre custom-btn px-2 h-100" id="genreDropdownBtn">
                        <p class="fs-0_98vw fw-bold m-0 select__placeholder">All genre</p>
                        <svg class="arrow" viewBox="0 0 12 16">
                            <path d="M6 10 L12 7 L6 16 L0 8 Z" fill="#d6dfff"/>
                        </svg>
                    </div>
                    <ul class="custom-menu">
                        @foreach ($genres as $genre)
                        <li data-value="{{ $genre->id }}">{{ $genre->name }}</li>
                        @endforeach
                    </ul>
                    <input type="hidden" name="genre_id" id="genreInput">
                </div>
                <div class="input-group flex-grow-1">
                    <button type="submit" class="border-0 bg-white d-flex align-items-center">
                        <img src="{{ asset('icon/search.svg') }}" class="search-icon">
                    </button>
                    <input class="form-control border-0 fs-0_98vw" type="search" placeholder="Search…" name="keyword" value="{{ old('keyword') }}">
                </div>
            </form>
        </div>
        @endif
    </header>
    <script>
        document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
            const btn = dropdown.querySelector('.custom-btn');
            const menu = dropdown.querySelector('.custom-menu');
            const input = dropdown.querySelector('input[type="hidden"]');

            btn.addEventListener('click', () => {
                menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
            });
            menu.querySelectorAll('li').forEach(item => {
                item.addEventListener('click', () => {
                    btn.querySelector('.select__placeholder').textContent = item.textContent;
                    btn.querySelector('.select__placeholder').style.fontSize = '';
                    btn.querySelector('.select__placeholder').style.color = '';
                    input.value = item.dataset.value;
                    menu.style.display = 'none';
                });
            });
            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) {
                    menu.style.display = 'none';
                }
            });
        });
    </script>

    <main>
        @yield('content')
    </main>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
        crossorigin="anonymous">
    </script>
</body>
</html>