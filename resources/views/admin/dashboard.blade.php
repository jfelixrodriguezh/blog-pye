@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-4">
        <h1 class="h2 fw-bold mb-1">Dashboard</h1>
        <p class="text-muted mb-0">Resumen general del sitio.</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <span class="text-muted small">Posts</span>
                    </div>
                    <div class="h3 fw-bold mb-0">{{ $stats['posts']['total'] }}</div>
                    <div class="text-muted small">{{ $stats['posts']['published'] }} publicados</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/></svg>
                        </div>
                        <span class="text-muted small">Podcasts</span>
                    </div>
                    <div class="h3 fw-bold mb-0">{{ $stats['podcasts'] }}</div>
                    <div class="text-muted small">programas</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-3 bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <span class="text-muted small">Episodios</span>
                    </div>
                    <div class="h3 fw-bold mb-0">{{ $stats['episodes']['total'] }}</div>
                    <div class="text-muted small">{{ $stats['episodes']['published'] }} publicados</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-3 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                        </div>
                        <span class="text-muted small">Himnos</span>
                    </div>
                    <div class="h3 fw-bold mb-0">{{ $stats['himnos']['total'] }}</div>
                    <div class="text-muted small">{{ $stats['himnos']['published'] }} publicados</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-3 bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <span class="text-muted small">Autores</span>
                    </div>
                    <div class="h3 fw-bold mb-0">{{ $stats['autores'] }}</div>
                    <div class="text-muted small">colaboradores</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-3 bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        </div>
                        <span class="text-muted small">Categorías</span>
                    </div>
                    <div class="h3 fw-bold mb-0">{{ $stats['categorias'] }}</div>
                    <div class="text-muted small">en el sitio</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Accesos rápidos</h6>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.posts.create') }}" class="btn btn-outline-primary btn-sm">+ Nuevo Post</a>
                <a href="{{ route('admin.podcasts.index') }}" class="btn btn-outline-success btn-sm">+ Nuevo Podcast</a>
                <a href="{{ route('admin.episodes.create') }}" class="btn btn-outline-info btn-sm">+ Nuevo Episodio</a>
                <a href="{{ route('admin.himnos.create') }}" class="btn btn-outline-warning btn-sm">+ Nuevo Himno</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Posts recientes</h6>
                    @forelse ($recentPosts as $post)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold small">{{ \Illuminate\Support\Str::limit($post->title, 40) }}</div>
                                <div class="text-muted small">{{ $post->autor->name ?? 'Sin autor' }}</div>
                            </div>
                            <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-light text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </a>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No hay posts todavía.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Episodios recientes</h6>
                    @forelse ($recentEpisodes as $episode)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold small">{{ \Illuminate\Support\Str::limit($episode->title, 40) }}</div>
                                <div class="text-muted small">{{ $episode->podcast->title }}</div>
                            </div>
                            <a href="{{ route('admin.episodes.edit', $episode) }}" class="btn btn-sm btn-light text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </a>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No hay episodios todavía.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
