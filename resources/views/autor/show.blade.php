@extends('layouts.public')

@section('title', $autor->name)

@section('content')
    <div class="card border-0 shadow-sm mb-4 autor-profile-header">
        <div class="card-body p-4 p-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-md-auto text-center">
                    @if ($autor->photo)
                        <img src="{{ asset('storage/'.$autor->photo) }}" class="rounded-circle" style="width:120px;height:120px;object-fit:cover;" alt="{{ $autor->name }}">
                    @else
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold mx-auto" style="width:120px;height:120px;font-size:2.25rem;">
                            {{ collect(explode(' ', $autor->name))->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}
                        </div>
                    @endif
                </div>
                <div class="col-md">
                    <h1 class="fw-bold mb-2">{{ $autor->name }}</h1>

                    @if ($autor->description)
                        <div class="autor-bio text-muted">
                            {!! $autor->description !!}
                        </div>
                    @else
                        <p class="text-muted mb-0">Este autor todavía no tiene una descripción.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center py-3 autor-stat">
                <div class="fs-3 fw-bold text-primary">{{ $postsCount }}</div>
                <div class="text-muted small">{{ \Illuminate\Support\Str::plural('Post', $postsCount) }}</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center py-3 autor-stat">
                <div class="fs-3 fw-bold text-primary">{{ $episodesCount }}</div>
                <div class="text-muted small">{{ \Illuminate\Support\Str::plural('Episodio', $episodesCount) }}</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center py-3 autor-stat">
                <div class="fs-3 fw-bold text-primary">{{ $himnosCount }}</div>
                <div class="text-muted small">{{ \Illuminate\Support\Str::plural('Himno', $himnosCount) }}</div>
            </div>
        </div>
    </div>

    @if ($recentPosts->isNotEmpty())
        <h2 class="h4 fw-bold mb-3">Posts</h2>
        <div class="row mb-4">
            @foreach ($recentPosts as $post)
                <div class="col-md-4 mb-4">
                    <a href="{{ route('post.show', $post) }}" class="card h-100 border-0 shadow-sm text-decoration-none text-reset">
                        <img src="{{ $post->post_image ? asset('storage/'.$post->post_image) : asset('images/grid-blog-style-1.jpg') }}"
                             class="card-img-top" style="height:160px;object-fit:cover;" alt="{{ $post->title }}">
                        <div class="card-body">
                            <h5 class="card-title h6">{{ $post->title }}</h5>
                            <div class="text-muted small">{{ $post->published_at?->format('d M, Y') }}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    @if ($recentEpisodes->isNotEmpty())
        <h2 class="h4 fw-bold mb-3">Episodios de podcast</h2>
        <div class="podcast-list mb-4">
            @foreach ($recentEpisodes as $episode)
                <a href="{{ route('podcasts.show', $episode) }}" class="podcast-item position-relative text-decoration-none text-reset">
                    <img src="{{ $episode->podcast->cover_image ? asset('storage/'.$episode->podcast->cover_image) : asset('images/grid-blog-style-1.jpg') }}" class="podcast-thumb" alt="{{ $episode->title }}">
                    <div class="podcast-body">
                        <div class="podcast-meta">
                            <span class="podcast-meta-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ $episode->published_at?->translatedFormat('d M, Y') }}
                            </span>
                        </div>
                        <h5 class="podcast-title">{{ $episode->title }}</h5>
                    </div>
                    <span class="podcast-play" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                    </span>
                </a>
            @endforeach
        </div>
    @endif

    @if ($recentHimnos->isNotEmpty())
        <h2 class="h4 fw-bold mb-3">Himnos</h2>
        <div class="d-flex flex-column gap-2">
            @foreach ($recentHimnos as $himno)
                <a href="{{ route('himnos.show', ['himnario' => $himno->himnario, 'numero' => $himno->numero]) }}"
                   class="card himno-item border-0 shadow-sm text-decoration-none text-reset">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $himno->titulo }}</div>
                            <div class="text-muted small">{{ $himno->himnario->nombre }}</div>
                        </div>
                        <span class="podcast-play himno-play flex-shrink-0" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    @if ($postsCount === 0 && $episodesCount === 0 && $himnosCount === 0)
        <p class="text-muted text-center py-5">Este autor todavía no tiene contenido publicado.</p>
    @endif
@endsection
