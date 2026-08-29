@extends('layouts.public')

@section('title', $himno->titulo)

@section('content')

    <style>
        .himno-info.ql-editor {
            padding: 0;
            min-height: auto;
            font-size: 1.05rem;
            line-height: 1.75;
        }
    </style>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('himnos.index') }}" class="text-decoration-none">Himnos</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $himno->titulo }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <h1 class="fw-bold mb-1">{{ $himno->titulo }}</h1>
                    <div class="text-muted">{{ $himno->himnario->nombre }}</div>
                </div>
                @if ($himno->category)
                    <span class="badge bg-primary-subtle text-primary fw-normal px-3 py-2">{{ $himno->category->name }}</span>
                @endif
            </div>

            @if ($himno->audio_url)
                <div class="mb-4">
                    <audio controls class="w-100">
                        <source src="{{ $himno->es_local ? route('audio.himno', $himno) : $himno->audio_url }}">
                        Tu navegador no soporta el elemento de audio.
                    </audio>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center" style="line-height:2;">
                            @foreach ($himno->estrofas as $estrofa)
                                <div class="mb-4">
                                    @if ($estrofa->tipo === 'estrofa')
                                        <div class="fw-semibold mb-1">{{ $estrofa->numero }}.</div>
                                    @endif
                                    <div style="white-space: pre-line;">{{ $estrofa->texto }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    @if ($himno->youtube_embed_url)
                        <div class="ratio ratio-16x9 mb-3">
                            <iframe src="{{ $himno->youtube_embed_url }}" title="{{ $himno->titulo }}"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        </div>
                    @endif

                    @if ($himno->partitura)
                        <img src="{{ $himno->partitura_es_local ? asset('storage/'.$himno->partitura) : $himno->partitura }}"
                             class="img-fluid rounded-3 border" alt="Partitura de {{ $himno->titulo }}">
                    @endif
                </div>
            </div>

            @if ($himno->informacion)
                <div class="mt-4 pt-4 border-top">
                    <h5 class="fw-bold mb-3">Acerca de este himno</h5>
                    <div class="himno-info ql-editor">
                        {!! $himno->informacion !!}
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            @if ($himno->autor)
                <div class="card border-0 shadow-sm mb-4 text-center">
                    <div class="card-body">
                        @if ($himno->autor->photo)
                            <img src="{{ asset('storage/'.$himno->autor->photo) }}" class="rounded-circle mb-3" style="width:80px;height:80px;object-fit:cover;" alt="{{ $himno->autor->name }}">
                        @else
                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width:80px;height:80px;font-size:1.5rem;">
                                {{ collect(explode(' ', $himno->autor->name))->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}
                            </div>
                        @endif
                        <div class="fw-bold mb-2">{{ $himno->autor->name }}</div>
                        @if ($himno->autor->description)
                            <p class="text-muted small mb-0">{{ $himno->autor->description }}</p>
                        @endif
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
                    <h6 class="fw-bold mb-3">Compartir este himno</h6>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;"
                                @click="navigator.share ? navigator.share({ title: @js($himno->titulo), url: window.location.href }) : copyLink()"
                                title="Compartir">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        </button>
                        <button type="button" class="btn btn-light rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;"
                                @click="copyLink()" title="Copiar enlace">
                            <svg x-show="!copied" x-cloak xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            <svg x-show="copied" x-cloak xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                        <a href="mailto:?subject={{ urlencode($himno->titulo) }}&body={{ urlencode(url()->current()) }}"
                           class="btn btn-light rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;" title="Enviar por correo">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
