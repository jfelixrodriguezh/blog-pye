@extends('layouts.public')

@section('title', 'Buscar en la Biblia')

@section('content')

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('biblia.index') }}" class="text-decoration-none">Biblia</a></li>
            <li class="breadcrumb-item active" aria-current="page">Buscar</li>
        </ol>
    </nav>

    <livewire:biblia.buscar />

@endsection
