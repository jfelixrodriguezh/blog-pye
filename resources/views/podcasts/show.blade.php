@extends('layouts.public')

@section('title', $episode->title)

@section('content')

    <style>
        .episode-content.ql-editor {
            padding: 0;
            min-height: auto;
            font-size: 1.05rem;
            line-height: 1.75;
        }
    </style>

    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                @if ($episode->categories->first())
                    <li class="breadcrumb-item">{{ $episode->categories->first()->name }}</li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($episode->title, 40) }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-9">
            <article>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($episode->categories as $category)
                            <span class="badge bg-primary-subtle text-primary fw-normal px-3 py-2">{{ $category->name }}</span>
                        @endforeach
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($episode->tags as $tag)
                            <span class="badge bg-white text-dark border px-3 py-2 fw-normal">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>

                <h1 class="fw-bold mb-3">{{ $episode->title }}</h1>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-4 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        @if ($episode->autor->photo)
                            <img src="{{ asset('storage/'.$episode->autor->photo) }}" class="rounded-circle" style="width:44px;height:44px;object-fit:cover;" alt="{{ $episode->autor->name }}">
                        @else
                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width:44px;height:44px;">
                                {{ collect(explode(' ', $episode->autor->name))->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}
                            </div>
                        @endif
                        <div class="text-muted small">
                            <span class="fw-semibold text-dark">{{ $episode->autor->name }}</span>
                            <br>
                            <span>{{ $episode->published_at?->translatedFormat('d \d\e F \d\e Y') }}</span>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">Episodio de</div>
                        <div class="fw-semibold">{{ $episode->podcast->title }}</div>
                    </div>
                </div>

                <div class="mb-4">
                    @if ($episode->es_local)
                        <audio controls class="w-100">
                            <source src="{{ route('audio.episode', $episode) }}">
                            Tu navegador no soporta el elemento de audio.
                        </audio>
                    @elseif ($episode->is_spotify_embed)
                        <iframe src="{{ $episode->audio_url }}" width="100%" height="152"
                                frameborder="0" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                                loading="lazy"></iframe>
                    @else
                        <audio controls class="w-100">
                            <source src="{{ $episode->audio_url }}">
                            Tu navegador no soporta el elemento de audio.
                        </audio>
                    @endif
                </div>

                @if ($episode->summary)
                    <p class="lead text-muted">{{ $episode->summary }}</p>
                @endif

                @if ($episode->show_notes)
                    <div class="episode-content ql-editor">
                        {!! $episode->show_notes !!}
                    </div>
                @endif

                @if ($previous || $next)
                    <div class="d-flex justify-content-between gap-3 mt-5 pt-4 border-top">
                        <div>
                            @if ($previous)
                                <a href="{{ route('podcasts.show', $previous) }}" class="text-decoration-none">
                                    <div class="text-muted small mb-1">&larr; Anterior</div>
                                    <div class="fw-semibold text-dark">{{ \Illuminate\Support\Str::limit($previous->title, 40) }}</div>
                                </a>
                            @endif
                        </div>
                        <div class="text-end">
                            @if ($next)
                                <a href="{{ route('podcasts.show', $next) }}" class="text-decoration-none">
                                    <div class="text-muted small mb-1">Siguiente &rarr;</div>
                                    <div class="fw-semibold text-dark">{{ \Illuminate\Support\Str::limit($next->title, 40) }}</div>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </article>
        </div>

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm mb-4 text-center">
                <div class="card-body">
                    @if ($episode->autor->photo)
                        <img src="{{ asset('storage/'.$episode->autor->photo) }}" class="rounded-circle mb-3" style="width:80px;height:80px;object-fit:cover;" alt="{{ $episode->autor->name }}">
                    @else
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width:80px;height:80px;font-size:1.5rem;">
                            {{ collect(explode(' ', $episode->autor->name))->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}
                        </div>
                    @endif
                    <div class="fw-bold mb-2">{{ $episode->autor->name }}</div>
                    @if ($episode->autor->description)
                        <p class="text-muted small mb-0">{{ $episode->autor->description }}</p>
                    @endif
                </div>
            </div>

            @if ($related->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Episodios relacionados</h6>
                        @foreach ($related as $relatedEpisode)
                            <a href="{{ route('podcasts.show', $relatedEpisode) }}" class="d-flex gap-3 text-decoration-none text-reset mb-3">
                                <img src="{{ $relatedEpisode->podcast->cover_image ? asset('storage/'.$relatedEpisode->podcast->cover_image) : asset('images/grid-blog-style-1.jpg') }}"
                                     class="rounded-2 flex-shrink-0" style="width:56px;height:56px;object-fit:cover;" alt="">
                                <div>
                                    <div class="fw-semibold small">{{ \Illuminate\Support\Str::limit($relatedEpisode->title, 45) }}</div>
                                    <div class="text-muted small">{{ $relatedEpisode->published_at?->format('d M, Y') }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm" x-data="{
                copied: false,
                copyLink() {
                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(window.location.href);
                    } else {
                        const textarea = document.createElement('textarea');
                        textarea.value = window.location.href;
                        textarea.style.position = 'fixed';
                        textarea.style.opacity = '0';
                        document.body.appendChild(textarea);
                        textarea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textarea);
                    }
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                }
            }">
                <div class="card-body text-center">
                    <h6 class="fw-bold mb-3">Compartir este episodio</h6>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;"
                                @click="navigator.share ? navigator.share({ title: @js($episode->title), url: window.location.href }) : copyLink()"
                                title="Compartir">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        </button>
                        <button type="button" class="btn btn-light rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;"
                                @click="copyLink()" title="Copiar enlace">
                            <svg x-show="!copied" x-cloak xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            <svg x-show="copied" x-cloak xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                        <a href="mailto:?subject={{ urlencode($episode->title) }}&body={{ urlencode(url()->current()) }}"
                           class="btn btn-light rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;" title="Enviar por correo">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
