@extends('layouts.app')

@section('title', 'Tags')

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Tags</h1>
            <p class="text-muted mb-0">Etiquetas para clasificar posts con más detalle.</p>
        </div>
    </div>

    <livewire:admin.tags.index />
@endsection
