<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — {{ config('app.name', 'Peregrinos y Extranjeros') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">

    @vite([
        'resources/css/app.scss',
        'resources/scss/light/assets/main.scss',
        'resources/js/app.js',
    ])
    <style>
        [x-cloak] { display: none !important; }
        .modal-overlay-centered {
            position: fixed;
            inset: 0;
            z-index: 1055;
            background: rgba(0,0,0,.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .site-header {
            background: #fff;
            border-bottom: 1px solid #e0e6ed;
            position: relative;
            z-index: 1045;
        }
        .site-nav {
            background: #fff;
            border-bottom: 1px solid #e0e6ed;
            position: relative;
            z-index: 1040;
        }
        .dropdown-menu {
            z-index: 1060 !important;
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
            text-decoration: none;
            transition: background-color .15s ease, color .15s ease;
        }
        .site-nav .nav-link svg { width: 18px; height: 18px; flex-shrink: 0; }
        .site-nav .nav-link:hover:not(.active) { background-color: #f1f2f3; }
        .site-nav .nav-link.active { background-color: var(--bs-primary); color: #fff; }
        .site-nav .dropdown-menu {
            font-size: .9rem;
        }
        .site-nav .dropdown-item.fw-bold {
            color: var(--bs-primary);
        }

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
            .site-nav .dropdown { width: 100%; }
            .site-nav .dropdown-menu { width: 100%; }
            body.dark .site-nav .nav-list { background: #1b2e4b; }
        }

        body.dark { background-color: #0e1726 !important; color: #bfc9d4; }
        body.dark .site-header,
        body.dark .site-nav { background-color: #1b2e4b; border-color: #191e3a; }
        body.dark .site-header a { color: #e0e6ed; }
        body.dark .site-nav .nav-link { color: #bfc9d4; }
        body.dark .site-nav .nav-link:hover:not(.active) { background-color: #191e3a; }
        body.dark .card,
        body.dark .statbox { background-color: #0e1726; color: #bfc9d4; }
        body.dark a.card.text-reset { color: #bfc9d4 !important; }
        body.dark .card-title { color: #e0e6ed; }
        body.dark .border-top,
        body.dark .border-bottom { border-color: #191e3a !important; }

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
        body.dark .table { color: #bfc9d4; }
        body.dark .table > :not(caption) > * > * { background-color: #0e1726; color: #bfc9d4; }

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
        body.dark .dropdown-menu {
            background-color: #1b2e4b;
            border-color: #191e3a;
        }
        body.dark .dropdown-item { color: #bfc9d4; }
        body.dark .dropdown-item:hover,
        body.dark .dropdown-item:focus { background-color: #191e3a; color: #e0e6ed; }
        body.dark .dropdown-divider { border-color: #191e3a; }

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
    </style>
    @livewireStyles
</head>
<body class="bg-light">

    <header class="site-header">
        <div class="container-xxl d-flex align-items-center justify-content-between py-3 gap-3">
            <a href="{{ route('admin.posts.index') }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark fw-bold fs-4 flex-shrink-0">
                <span class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:1rem;">C</span>
                Peregrinos y Extranjeros
            </a>

            <div class="d-none d-md-block flex-grow-1" style="max-width: 480px;">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" class="form-control bg-light border-0" placeholder="Search..." disabled>
                    <span class="input-group-text bg-light border-0 text-muted small">Ctrl + /</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                <button type="button" class="btn btn-link p-0 text-muted theme-toggle-btn" id="themeToggleBtn" title="Cambiar tema">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-icon="moon"><path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-icon="sun" class="d-none"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>
                </button>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                        @if (auth()->user()->photo)
                            <img src="{{ asset('storage/'.auth()->user()->photo) }}" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;" alt="{{ auth()->user()->name }}">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:36px;height:36px;">
                                {{ collect(explode(' ', auth()->user()->name))->map(fn($p) => mb_substr($p,0,1))->join('') }}
                            </div>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <nav class="site-nav">
        <div class="container-xxl">
            <div class="d-flex d-md-none align-items-center gap-2 nav-toolbar">
                <button type="button" class="nav-toggle-btn d-flex align-items-center flex-shrink-0" id="navToggleBtn" aria-expanded="false" aria-controls="adminNavList">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                    Menú
                </button>
                <div class="input-group flex-grow-1">
                    <span class="input-group-text bg-light border-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" class="form-control bg-light border-0" placeholder="Search..." disabled>
                </div>
            </div>
        <div class="d-flex nav-list py-2 gap-2 align-items-center flex-wrap" id="adminNavList">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                Dashboard
            </a>

            <div class="dropdown">
                <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Posts
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item {{ request()->routeIs('admin.posts.*') ? 'fw-bold' : '' }}" href="{{ route('admin.posts.index') }}">Todos los posts</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.autors.*') ? 'fw-bold' : '' }}" href="{{ route('admin.autors.index') }}">Autores</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.categories.*') ? 'fw-bold' : '' }}" href="{{ route('admin.categories.index') }}">Categorías</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.tags.*') ? 'fw-bold' : '' }}" href="{{ route('admin.tags.index') }}">Tags</a></li>
                </ul>
            </div>

            <div class="dropdown">
                <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs('admin.podcasts.*') || request()->routeIs('admin.episodes.*') ? 'active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/></svg>
                    Podcasts
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item {{ request()->routeIs('admin.podcasts.*') ? 'fw-bold' : '' }}" href="{{ route('admin.podcasts.index') }}">Podcasts</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.episodes.*') ? 'fw-bold' : '' }}" href="{{ route('admin.episodes.index')}}">Episodios</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.autors.*') ? 'fw-bold' : '' }}" href="{{ route('admin.autors.index') }}">Autores</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.categories.*') ? 'fw-bold' : '' }}" href="{{ route('admin.categories.index') }}">Categorías</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.tags.*') ? 'fw-bold' : '' }}" href="{{ route('admin.tags.index') }}">Tags</a></li>
                </ul>
            </div>

            <div class="dropdown">
                <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs('admin.himnarios.*') || request()->routeIs('admin.tonos.*') || request()->routeIs('admin.himnos.*') ? 'active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    Himnos
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item {{ request()->routeIs('admin.himnarios.*') ? 'fw-bold' : '' }}" href="{{ route('admin.himnarios.index') }}">Himnarios</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.tonos.*') ? 'fw-bold' : '' }}" href="{{ route('admin.tonos.index')}}">Tonos</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.autors.*') ? 'fw-bold' : '' }}" href="{{ route('admin.autors.index') }}">Autores</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.categories.*') ? 'fw-bold' : '' }}" href="{{ route('admin.categories.index') }}">Categorías</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.himnos.*') ? 'fw-bold' : '' }}" href="{{ route('admin.himnos.index') }}">Himnos</a></li>
                </ul>
            </div>

            @can('manage users')
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Usuarios
                </a>
            @endcan

            @can('manage users')
                <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Roles
                </a>
            @endcan
        </div>
        </div>
    </nav>

    <main class="container-xxl py-4">
        @yield('content')
    </main>
    @livewireScripts

    <script>
        (function () {
            var body = document.body;
            var btn = document.getElementById('themeToggleBtn');
            if (!btn) { return; }
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
            var navList = document.getElementById('adminNavList');
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
