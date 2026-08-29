<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Peregrinos y Extranjeros'))</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">

    {{-- Reusamos Bootstrap + los overrides base de CORK (botones, cards, badges).
         NO cargamos resources/scss/layouts/* porque eso es el sidebar/navbar del panel admin. --}}
    @vite([
        'resources/css/app.scss',
        'resources/scss/light/assets/main.scss',
        'resources/js/app.js',
    ])
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        .cork-nav {
            background-color: #191e3a;
        }
        .cork-nav .nav-item {
            position: relative;
        }
        .cork-nav .nav-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 14px;
            bottom: 14px;
            right: 0;
            width: 1px;
            background: #515365;
        }
        .cork-nav .nav-link {
            color: #e0e6ed;
        }
        .cork-nav .nav-link.active {
            color: #ffffff;
            font-weight: 600;
        }
        .cork-nav .nav-link:hover:not(.disabled) {
            color: #ffffff;
        }
        .cork-nav .nav-link.disabled {
            color: #6b7086;
            pointer-events: none;
        }
        .cork-nav svg {
            width: 24px;
            height: 24px;
        }
        .ts-wrapper.form-select,
        .ts-wrapper.form-control {
            font-size: 15px;
            min-height: calc(1.5em + 1.5rem + calc(var(--bs-border-width) * 2));
        }
        .ts-wrapper.form-select .ts-control,
        .ts-wrapper.form-control .ts-control {
            padding: 0.75rem 1.25rem;
            letter-spacing: 1px;
        }
        .card.style-3 .card-img,
        .card.style-3 .card-img-top {
            border-radius: 10px;
            box-shadow: 0 6px 10px 0 rgba(0, 0, 0, 0.14), 0 1px 18px 0 rgba(0, 0, 0, 0.12), 0 3px 5px -1px rgba(0, 0, 0, 0.2);
            width: 40%;
            margin-right: 12px;
        }
    </style>
</head>
<body class="bg-light">

    <header class="bg-white border-bottom">
        <div class="container-xxl d-flex align-items-center justify-content-between py-3 gap-3">
            <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark fw-bold fs-4 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4361ee" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V10l7-6 7 6v11"/><path d="M12 21v-6"/></svg>
                Peregrinos y Extranjeros
            </a>

            <form action="{{ route('search') }}" method="GET" class="flex-grow-1" style="max-width: 480px;">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-0" placeholder="Buscar...">
                    <span class="input-group-text bg-light border-0 text-muted small">Ctrl + /</span>
                </div>
            </form>

            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                <a href="#" class="text-dark" title="Modo oscuro (próximamente)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>
                </a>
                <a href="#" class="text-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                </a>
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:36px;height:36px;font-size:.8rem;">GT</div>
            </div>
        </div>
    </header>

    <nav class="cork-nav">
        <div class="container-xxl">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 py-3 px-3 {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                        Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 py-3 px-3 {{ request()->routeIs('biblia.*') ? 'active' : '' }}" href="{{ route('biblia.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        Biblia Online
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 py-3 px-3 {{ request()->routeIs('podcasts.*') ? 'active' : '' }}" href="{{ route('podcasts.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/></svg>
                        Podcasts
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 py-3 px-3 {{ request()->routeIs('himnos.*') ? 'active' : '' }}" href="{{ route('himnos.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                        Himnos
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 py-3 px-3 disabled" href="#" tabindex="-1" aria-disabled="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        Revistas
                    </a>
                </li> -->
            </ul>
        </div>
    </nav>

    <main class="container-xxl py-4">
        @yield('content')
    </main>

    <footer class="border-top bg-white py-3 mt-5">
        <div class="container-xxl d-flex justify-content-between text-muted small">
            <span>Copyright &copy; {{ date('Y') }} Peregrinos y Extranjeros. Todos los derechos reservados.</span>
            <span>Dios te bendiga
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </span>
        </div>
    </footer>
    @livewireScripts
</body>
</html>
