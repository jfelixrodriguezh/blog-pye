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
    </style>
    @livewireStyles
</head>
<body class="bg-light">

    <header class="bg-white border-bottom">
        <div class="container-xxl d-flex align-items-center justify-content-between py-3 gap-3">
            <a href="{{ route('admin.posts.index') }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark fw-bold fs-4 flex-shrink-0">
                <span class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:1rem;">C</span>
                Peregrinos y Extranjeros
            </a>

            <div class="flex-grow-1" style="max-width: 480px;">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" class="form-control bg-light border-0" placeholder="Search..." disabled>
                    <span class="input-group-text bg-light border-0 text-muted small">Ctrl + /</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                <a href="#" class="text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                </a>
                <a href="#" class="text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>
                </a>
                <a href="#" class="text-muted position-relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-primary border border-light rounded-circle"></span>
                </a>
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:36px;height:36px;font-size:.8rem;">GT</div>
            </div>
        </div>
    </header>

    <nav class="cork-nav">
        <div class="container-xxl">
            <ul class="nav">
                <a class="nav-link d-flex align-items-center gap-2 py-3 px-3 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                    Dashboard
                </a>
                <li class="nav-item dropdown" x-data="{ open: false }" @click.away="open = false" style="position: relative;">
                    <a href="#"
                       @click.prevent="open = !open"
                       class="nav-link active d-flex align-items-center gap-2 py-3 px-3"
                       style="cursor:pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        Posts
                    </a>
                    <ul x-show="open"
                        style="display:none; position:absolute; top:100%; left:0; min-width:200px; margin:4px 0 0; padding:8px 0; list-style:none; background:#fff; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.15); z-index:1050;">
                        <li><a href="{{ route('admin.posts.index') }}" class="{{ request()->routeIs('admin.posts.*') ? 'fw-bold' : '' }}" style="display:block; padding:8px 16px; text-decoration:none; color:#212529;">Todos los posts</a></li>
                        <li><a href="{{ route('admin.autors.index') }}" class="{{ request()->routeIs('admin.autors.*') ? 'fw-bold' : '' }}" style="display:block; padding:8px 16px; text-decoration:none; color:#212529;">Autores</a></li>
                        <li><a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'fw-bold' : '' }}" style="display:block; padding:8px 16px; text-decoration:none; color:#212529;">Categorías</a></li>
                        <li><a href="{{ route('admin.tags.index') }}" class="{{ request()->routeIs('admin.tags.*') ? 'fw-bold' : '' }}" style="display:block; padding:8px 16px; text-decoration:none; color:#212529;">Tags</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown" x-data="{ open: false }" @click.away="open = false" style="position: relative;">
                    <a href="#"
                    @click.prevent="open = !open"
                    class="nav-link active d-flex align-items-center gap-2 py-3 px-3"
                    style="cursor:pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/></svg>
                        Podcasts
                    </a>
                    <ul x-show="open"
                        style="display:none; position:absolute; top:100%; left:0; min-width:200px; margin:4px 0 0; padding:8px 0; list-style:none; background:#fff; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.15); z-index:1050;">
                        <li><a href="{{ route('admin.podcasts.index') }}" class="{{ request()->routeIs('admin.podcasts.*') ? 'fw-bold' : '' }}" style="display:block; padding:8px 16px; text-decoration:none; color:#212529;">Podcasts</a></li>
                        <li><a href="{{ route('admin.episodes.index')}}" class="{{ request()->routeIs('admin.episodes.*') ? 'fw-bold' : '' }}" style="display:block; padding:8px 16px; text-decoration:none; color:#212529;">Episodios</a></li>
                        <li><a href="{{ route('admin.autors.index') }}" class="{{ request()->routeIs('admin.autors.*') ? 'fw-bold' : '' }}" style="display:block; padding:8px 16px; text-decoration:none; color:#212529;">Autores</a></li>
                        <li><a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'fw-bold' : '' }}" style="display:block; padding:8px 16px; text-decoration:none; color:#212529;">Categorías</a></li>
                        <li><a href="{{ route('admin.tags.index') }}" class="{{ request()->routeIs('admin.tags.*') ? 'fw-bold' : '' }}" style="display:block; padding:8px 16px; text-decoration:none; color:#212529;">Tags</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown" x-data="{ open: false }" @click.away="open = false" style="position: relative;">
                    <a href="#"
                    @click.prevent="open = !open"
                    class="nav-link active d-flex align-items-center gap-2 py-3 px-3"
                    style="cursor:pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                        Himnos
                    </a>
                    <ul x-show="open"
                        style="display:none; position:absolute; top:100%; left:0; min-width:200px; margin:4px 0 0; padding:8px 0; list-style:none; background:#fff; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.15); z-index:1050;">
                        <li><a href="{{ route('admin.himnarios.index') }}" class="{{ request()->routeIs('admin.podcasts.*') ? 'fw-bold' : '' }}" style="display:block; padding:8px 16px; text-decoration:none; color:#212529;">Himnarios</a></li>
                        <li><a href="{{ route('admin.tonos.index')}}" class="{{ request()->routeIs('admin.episodes.*') ? 'fw-bold' : '' }}" style="display:block; padding:8px 16px; text-decoration:none; color:#212529;">Tonos</a></li>
                        <li><a href="{{ route('admin.autors.index') }}" class="{{ request()->routeIs('admin.autors.*') ? 'fw-bold' : '' }}" style="display:block; padding:8px 16px; text-decoration:none; color:#212529;">Autores</a></li>
                        <li><a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'fw-bold' : '' }}" style="display:block; padding:8px 16px; text-decoration:none; color:#212529;">Categorías</a></li>
                        <li><a href="{{ route('admin.himnos.index') }}" class="{{ request()->routeIs('admin.tags.*') ? 'fw-bold' : '' }}" style="display:block; padding:8px 16px; text-decoration:none; color:#212529;">Himnos</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 py-3 px-3 disabled" href="#" tabindex="-1" aria-disabled="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        Analytics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 py-3 px-3 disabled" href="#" tabindex="-1" aria-disabled="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        Settings
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <main class="container-xxl py-4">
        @yield('content')
    </main>
    @livewireScripts
</body>
</html>
