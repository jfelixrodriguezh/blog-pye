<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Peregrinos y Extranjeros'))</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">

    {{-- Bootstrap + overrides base de CORK (botones, cards, badges, variables de color).
         El header y la barra de navegación de esta plantilla son propios (CSS/JS a medida
         más abajo) en vez del bundle admin de Cork (horizontal-light-menu), que trae JS
         viejo pensado para un layout con sidebar y rompe en una página sin sidebar. --}}
    @vite([
        'resources/css/app.scss',
        'resources/scss/light/assets/main.scss',
        'resources/scss/dark/assets/main.scss',
        'resources/js/app.js',
    ])
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }

        html, body { height: 100%; }
        body.bg-light {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        body.bg-light > main { flex: 1 0 auto; }
        body.bg-light > footer.site-footer { flex-shrink: 0; }

        body.dark { background-color: #0e1726 !important; color: #bfc9d4; }
        body.dark .site-header,
        body.dark .site-nav { background-color: #1b2e4b; border-color: #191e3a; }
        body.dark .site-header a.brand { color: #e0e6ed !important; }
        body.dark .site-nav .nav-link { color: #bfc9d4; }
        body.dark .site-nav .nav-link:hover:not(.active) { background-color: #191e3a; }
        body.dark .card { background-color: #0e1726; color: #bfc9d4; }
        body.dark a.card.text-reset { color: #bfc9d4 !important; }
        body.dark .card-title { color: #e0e6ed; }
        body.dark .border-top,
        body.dark .border-bottom { border-color: #191e3a !important; }
        body.dark footer.site-footer { background-color: #1b2e4b !important; border-color: #191e3a !important; color: #bfc9d4; }

        body.dark .form-select,
        body.dark .form-control {
            background-color: #0e1726;
            border-color: #191e3a;
            color: #bfc9d4;
        }
        body.dark .form-select:focus,
        body.dark .form-control:focus {
            background-color: #0e1726;
            border-color: var(--bs-primary);
            color: #bfc9d4;
            box-shadow: none;
        }
        body.dark .form-select option { background-color: #0e1726; color: #bfc9d4; }

        body.dark .page-link {
            background-color: #0e1726;
            border-color: #191e3a;
            color: #bfc9d4;
        }
        body.dark .page-link:hover { background-color: #191e3a; color: #e0e6ed; }
        body.dark .page-item.active .page-link {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            color: #fff;
        }
        body.dark .page-item.disabled .page-link {
            background-color: #0e1726;
            border-color: #191e3a;
            color: #566078;
        }

        body.dark .site-header .input-group .form-control,
        body.dark .site-header .input-group-text {
            background-color: #1b2e4b !important;
            color: #bfc9d4;
        }
        body.dark .badge.bg-light { background-color: #191e3a !important; color: #bfc9d4 !important; }

        .site-header {
            background: #fff;
            border-bottom: 1px solid #e0e6ed;
        }
        .site-header .brand {
            font-size: 1.25rem;
        }
        .site-header .brand svg { flex-shrink: 0; }

        .site-nav {
            background: #fff;
            border-bottom: 1px solid #e0e6ed;
            position: relative;
            z-index: 1040;
        }
        .site-nav .dropdown-menu {
            z-index: 1050 !important;
        }
        .site-nav .nav-link {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .55rem 1rem;
            border-radius: .5rem;
            color: #3b3f5c;
            font-weight: 600;
            font-size: .9rem;
            white-space: nowrap;
            transition: background-color .15s ease, color .15s ease;
        }
        .site-nav .nav-link svg { width: 18px; height: 18px; flex-shrink: 0; }
        .site-nav .nav-link:hover:not(.active) { background-color: #f1f2f3; }
        .site-nav .nav-link.active { background-color: var(--bs-primary); color: #fff; }

        .site-nav .container-xxl { position: relative; }
        .nav-toolbar { padding: .5rem 0; }
        .nav-toggle-btn {
            align-items: center;
            gap: .5rem;
            background: none;
            border: 1px solid #e0e6ed;
            border-radius: .5rem;
            padding: .5rem .9rem;
            color: #3b3f5c;
            font-weight: 600;
            font-size: .9rem;
        }
        .nav-toggle-btn svg { width: 18px; height: 18px; flex-shrink: 0; }
        body.dark .nav-toggle-btn { color: #bfc9d4; border-color: #191e3a; }

        .nav-toolbar .input-group {
            border: 1px solid #e0e6ed;
            border-radius: .5rem;
            overflow: hidden;
        }
        .nav-toolbar .input-group-text,
        .nav-toolbar .form-control {
            background: #fff;
            border: 0;
        }
        .nav-toolbar .form-control:focus { box-shadow: none; }
        body.dark .nav-toolbar .input-group { border-color: #191e3a; }
        body.dark .nav-toolbar .input-group-text,
        body.dark .nav-toolbar .form-control { background: #0e1726; color: #bfc9d4; }

        @media (max-width: 767.98px) {
            .site-nav .nav-list {
                display: none !important;
                flex-direction: column;
                flex-wrap: nowrap !important;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: #fff;
                box-shadow: 0 12px 30px rgba(0, 0, 0, .15);
                border-radius: 0 0 14px 14px;
                padding: .5rem;
                margin: 0;
                z-index: 1045;
            }
            .site-nav .nav-list.show { display: flex !important; }
            .site-nav .nav-link { width: 100%; }
            body.dark .site-nav .nav-list { background: #1b2e4b; }
        }

        .theme-toggle-btn svg { width: 20px; height: 20px; }

        .card.style-3 .card-img,
        .card.style-3 .card-img-top {
            border-radius: 10px;
            box-shadow: 0 6px 10px 0 rgba(0, 0, 0, 0.14), 0 1px 18px 0 rgba(0, 0, 0, 0.12), 0 3px 5px -1px rgba(0, 0, 0, 0.2);
            width: 40%;
            margin-right: 12px;
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

        /* Listado de episodios de podcast (adaptado de blog_cork/podcast.html) */
        .podcast-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        @media (max-width: 991.98px) {
            .podcast-list { grid-template-columns: 1fr; }
        }
        .podcast-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 18px 20px;
            border: 1px solid var(--bs-border-color, #e0e6ed);
            border-radius: 12px;
            background: #fff;
            transition: box-shadow .2s ease, border-color .2s ease;
        }
        .podcast-item:hover {
            border-color: var(--bs-primary);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }
        .podcast-thumb {
            width: 72px;
            height: 72px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
        }
        .podcast-body { flex: 1 1 auto; min-width: 0; }
        .podcast-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 12.5px;
            color: #888ea8;
            margin-bottom: 6px;
        }
        .podcast-meta-item { display: inline-flex; align-items: center; gap: 5px; }
        .podcast-meta-sep { color: #bfc9d4; }
        .podcast-title { font-size: 16px; font-weight: 700; margin-bottom: 6px; }
        .podcast-title a:hover { color: var(--bs-primary); }
        .podcast-desc {
            font-size: 13.5px;
            color: #888ea8;
            margin-bottom: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .podcast-play {
            flex-shrink: 0;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 1px solid var(--bs-border-color, #e0e6ed);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--bs-primary);
            transition: background .2s ease, color .2s ease, border-color .2s ease;
        }
        .podcast-item:hover .podcast-play {
            background: var(--bs-primary);
            border-color: var(--bs-primary);
            color: #fff;
        }
        @media (max-width: 575.98px) {
            .podcast-item { padding: 14px; gap: 14px; }
            .podcast-thumb { width: 56px; height: 56px; }
            .podcast-title { font-size: 14.5px; }
        }
        body.dark .podcast-item { background: #0e1726; border-color: #191e3a; }
        body.dark .podcast-meta,
        body.dark .podcast-desc { color: #888ea8; }
        body.dark .podcast-play { border-color: #191e3a; }
    </style>
</head>
<body class="bg-light">

    <header class="site-header">
        <div class="container-xxl d-flex align-items-center justify-content-between py-3 gap-3">
            <a href="{{ route('home') }}" class="brand d-flex align-items-center gap-2 text-decoration-none text-dark fw-bold flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4361ee" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V10l7-6 7 6v11"/><path d="M12 21v-6"/></svg>
                Peregrinos y Extranjeros
            </a>

            <form action="{{ route('search') }}" method="GET" class="d-none d-md-block flex-grow-1" style="max-width: 480px;">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-0" placeholder="Buscar...">
                    <span class="input-group-text bg-light border-0 text-muted small d-none d-md-flex">Ctrl + /</span>
                </div>
            </form>

            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                <button type="button" class="btn btn-link p-0 text-dark theme-toggle-btn" id="themeToggleBtn" title="Cambiar tema">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-icon="moon"><path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-icon="sun" class="d-none"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>
                </button>

                @auth
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                            @if (auth()->user()->photo)
                                <img src="{{ asset('storage/'.auth()->user()->photo) }}" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;" alt="{{ auth()->user()->name }}">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:36px;height:36px;font-size:.8rem;">
                                    {{ collect(explode(' ', auth()->user()->name))->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}
                                </div>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span></li>
                            @can('manage users')
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Panel admin</a></li>
                            @endcan
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Cerrar sesión</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-dark" title="Iniciar sesión">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <nav class="site-nav">
        <div class="container-xxl">
            <div class="d-flex d-md-none align-items-center gap-2 nav-toolbar">
                <button type="button" class="nav-toggle-btn d-flex align-items-center flex-shrink-0" id="navToggleBtn" aria-expanded="false" aria-controls="siteNavList">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                    Menú
                </button>
                <form action="{{ route('search') }}" method="GET" class="flex-grow-1">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-0" placeholder="Buscar...">
                    </div>
                </form>
            </div>
            <div class="d-flex nav-list py-2 gap-2 flex-wrap" id="siteNavList">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                    Home
                </a>
                <a class="nav-link {{ request()->routeIs('biblia.*') ? 'active' : '' }}" href="{{ route('biblia.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    Biblia Online
                </a>
                <a class="nav-link {{ request()->routeIs('podcasts.*') ? 'active' : '' }}" href="{{ route('podcasts.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/></svg>
                    Podcast
                </a>
                <a class="nav-link {{ request()->routeIs('himnos.*') ? 'active' : '' }}" href="{{ route('himnos.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    Himnos
                </a>
            </div>
        </div>
    </nav>

    <main class="container-xxl py-4">
        @yield('content')
    </main>

    <footer class="site-footer border-top bg-white py-3 mt-5">
        <div class="container-xxl d-flex justify-content-between text-muted small">
            <span>Copyright &copy; {{ date('Y') }} Peregrinos y Extranjeros. Todos los derechos reservados.</span>
            <span>Dios te bendiga
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </span>
        </div>
    </footer>

    @livewireScripts

    <script>
        (function () {
            var root = document.documentElement;
            var body = document.body;
            var btn = document.getElementById('themeToggleBtn');
            var moonIcon = btn.querySelector('[data-icon="moon"]');
            var sunIcon = btn.querySelector('[data-icon="sun"]');

            function applyTheme(theme) {
                if (theme === 'dark') {
                    body.classList.add('dark');
                    moonIcon.classList.add('d-none');
                    sunIcon.classList.remove('d-none');
                } else {
                    body.classList.remove('dark');
                    sunIcon.classList.add('d-none');
                    moonIcon.classList.remove('d-none');
                }
            }

            var saved = null;
            try { saved = localStorage.getItem('pye-theme'); } catch (e) {}
            applyTheme(saved === 'dark' ? 'dark' : 'light');

            btn.addEventListener('click', function () {
                var next = body.classList.contains('dark') ? 'light' : 'dark';
                applyTheme(next);
                try { localStorage.setItem('pye-theme', next); } catch (e) {}
            });
        })();

        (function () {
            var navToggleBtn = document.getElementById('navToggleBtn');
            var navList = document.getElementById('siteNavList');
            if (!navToggleBtn || !navList) return;

            function closeNav() {
                navList.classList.remove('show');
                navToggleBtn.setAttribute('aria-expanded', 'false');
            }

            navToggleBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                var isOpen = navList.classList.toggle('show');
                navToggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            document.addEventListener('click', function (e) {
                if (!navList.classList.contains('show')) return;
                if (navList.contains(e.target) || navToggleBtn.contains(e.target)) return;
                closeNav();
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) closeNav();
            });
        })();
    </script>
</body>
</html>
