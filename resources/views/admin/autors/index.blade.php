@extends('layouts.app')

@section('title', 'Autores')

@section('content')
    <div class="mb-4">
        <h1 class="h2 fw-bold mb-1">Autores</h1>
        <p class="text-muted mb-0">Gestiona los autores del blog.</p>
    </div>

    <livewire:admin.autors.index />
@endsection
