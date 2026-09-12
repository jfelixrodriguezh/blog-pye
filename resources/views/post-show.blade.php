@extends('layouts.public')

@section('title', $post->title)

@section('content')

    <style>
        .post-content.ql-editor {
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
                @if ($post->category)
                    <li class="breadcrumb-item">{{ $post->category->name }}</li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($post->title, 40) }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-9">
            <article>
                @if ($post->post_image)
                    <img src="{{ asset('storage/'.$post->post_image) }}" class="img-fluid rounded-3 mb-3 w-100" style="max-height:420px; object-fit:cover;" alt="{{ $post->title }}">
                @endif

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    @if ($post->category)
                        <span class="badge bg-primary-subtle text-primary fw-normal px-3 py-2">{{ $post->category->name }}</span>
                    @else
                        <span></span>
                    @endif
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($post->tags as $tag)
                            <span class="badge bg-white text-dark border px-3 py-2 fw-normal">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>

                <h1 class="fw-bold mb-3 text-center">{{ $post->title }}</h1>

                <div class="d-flex align-items-center gap-2 pb-4 border-bottom">
                    <a href="{{ route('autor.show', $post->autor) }}" class="flex-shrink-0">
                        @if ($post->autor->photo)
                            <img src="{{ asset('storage/'.$post->autor->photo) }}" class="rounded-circle" style="width:44px;height:44px;object-fit:cover;" alt="{{ $post->autor->name }}">
                        @else
                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width:44px;height:44px;">
                                {{ collect(explode(' ', $post->autor->name))->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}
                            </div>
                        @endif
                    </a>
                    <div class="text-muted small d-flex align-items-center gap-2 flex-wrap">
                        <a href="{{ route('autor.show', $post->autor) }}" class="fw-semibold text-dark text-decoration-none">{{ $post->autor->name }}</a>
                        <span>&bull;</span>
                        <span>{{ $post->published_at?->translatedFormat('d \d\e F \d\e Y') }}</span>
                        <span>&bull;</span>
                        <span class="d-inline-flex align-items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ $post->reading_time }} min de lectura
                        </span>
                    </div>
                </div>

                <div class="post-content ql-editor">
                    {!! $post->content !!}
                </div>

                @if ($previous || $next)
                    <div class="d-flex justify-content-between gap-3 pt-4 border-top">
                        <div>
                            @if ($previous)
                                <a href="{{ route('post.show', $previous) }}" class="text-decoration-none">
                                    <div class="text-muted small mb-1">&larr; Anterior</div>
                                    <div class="fw-semibold text-dark">{{ \Illuminate\Support\Str::limit($previous->title, 40) }}</div>
                                </a>
                            @endif
                        </div>
                        <div class="text-end">
                            @if ($next)
                                <a href="{{ route('post.show', $next) }}" class="text-decoration-none">
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
            <x-autor-card :autor="$post->autor" />

            @if ($related->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Posts relacionados</h6>
                        @foreach ($related as $relatedPost)
                            <a href="{{ route('post.show', $relatedPost) }}" class="d-flex gap-3 text-decoration-none text-reset mb-3">
                                <img src="{{ $relatedPost->post_image ? asset('storage/'.$relatedPost->post_image) : asset('images/grid-blog-style-1.jpg') }}"
                                     class="rounded-2 flex-shrink-0" style="width:56px;height:56px;object-fit:cover;" alt="">
                                <div>
                                    <div class="fw-semibold small">{{ \Illuminate\Support\Str::limit($relatedPost->title, 45) }}</div>
                                    <div class="text-muted small">{{ $relatedPost->published_at?->format('d M, Y') }}</div>
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
                    <h6 class="fw-bold mb-3">Compartir este artículo</h6>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;"
                            @click="navigator.share ? navigator.share({ title: @js($post->title), url: window.location.href }) : copyLink()"
                            title="Compartir">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        </button>
                        <button type="button" class="btn btn-light rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;"
                                @click="copyLink()" title="Copiar enlace">
                            <svg x-show="!copied" x-cloak xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            <svg x-show="copied" x-cloak xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                        <a href="mailto:?subject={{ urlencode($post->title) }}&body={{ urlencode(url()->current()) }}"
                           class="btn btn-light rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;" title="Enviar por correo">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
