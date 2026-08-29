@extends('layouts.app')

@section('title', 'Himnarios')

@section('content')
    <div class="mb-4">
        <h1 class="h2 fw-bold mb-1">Himnarios</h1>
        <p class="text-muted mb-0">Gestiona los himnarios (libros de himnos).</p>
    </div>

    <livewire:admin.himnarios.index />
@endsection
