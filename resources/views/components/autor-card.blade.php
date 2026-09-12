@props(['autor'])

<div class="card border-0 shadow-sm mb-4 text-center autor-card">
    <div class="card-body">
        <a href="{{ route('autor.show', $autor) }}" class="text-reset text-decoration-none d-block">
            @if ($autor->photo)
                <img src="{{ asset('storage/'.$autor->photo) }}" class="rounded-circle mb-3" style="width:80px;height:80px;object-fit:cover;" alt="{{ $autor->name }}">
            @else
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width:80px;height:80px;font-size:1.5rem;">
                    {{ collect(explode(' ', $autor->name))->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}
                </div>
            @endif
            <div class="fw-bold mb-2">{{ $autor->name }}</div>
        </a>

        @if ($autor->description)
            <p class="text-muted small mb-3">{{ \Illuminate\Support\Str::limit(strip_tags($autor->description), 110) }}</p>
        @endif

        <a href="{{ route('autor.show', $autor) }}" class="btn btn-sm btn-outline-primary">Ver perfil</a>
    </div>
</div>
