@extends('layouts.public')

@section('title', 'Buscar')

@section('content')

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Búsqueda</li>
        </ol>
    </nav>

    <livewire:search.results />

@endsection
