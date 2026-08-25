@extends('layouts.public')

@section('title', $libro->nombre.' '.$capitulo)

@section('content')

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('biblia.index') }}" class="text-decoration-none">Biblia</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $libro->nombre }} {{ $capitulo }}</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-8">
                    <label class="form-label small text-muted">Libro</label>
                    <select onchange="if(this.value) window.location.href=this.value" class="form-select">
                        @foreach ($testamentos as $testamento)
                            <optgroup label="{{ $testamento->nombre }}">
                                @foreach ($testamento->libros as $l)
                                    <option value="{{ route('biblia.leer', ['libro' => $l, 'capitulo' => 1]) }}" @selected($l->id === $libro->id)>
                                        {{ $l->nombre }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Capítulo</label>
                    <select onchange="if(this.value) window.location.href=this.value" class="form-select">
                        @for ($c = 1; $c <= $totalCapitulos; $c++)
                            <option value="{{ route('biblia.leer', ['libro' => $libro, 'capitulo' => $c]) }}" @selected($c === $capitulo)>
                                Capítulo {{ $c }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="col-12 mt-2">
                <a href="{{ route('biblia.buscar') }}" class="text-decoration-none small">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Buscar en la Biblia
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-lg-5">
            <h1 class="fw-bold mb-4">{{ $libro->nombre }} {{ $capitulo }}</h1>

            <div class="fs-5" style="line-height: 2.2;">
                @foreach ($versiculos as $v)
                    <p id="v{{ $v->numero }}" class="mb-2">
                        <sup class="text-muted me-1">{{ $v->numero }}</sup>{{ $v->texto }}
                    </p>
                @endforeach
            </div>

            <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                <div>
                    @if ($anterior)
                        <a href="{{ route('biblia.leer', $anterior) }}" class="btn btn-outline-secondary">
                            &larr; {{ $anterior['libro']->nombre }} {{ $anterior['capitulo'] }}
                        </a>
                    @endif
                </div>
                <div>
                    @if ($siguiente)
                        <a href="{{ route('biblia.leer', $siguiente) }}" class="btn btn-outline-secondary">
                            {{ $siguiente['libro']->nombre }} {{ $siguiente['capitulo'] }} &rarr;
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
