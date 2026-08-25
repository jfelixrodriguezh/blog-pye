@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Categorías</h1>
            <p class="text-muted mb-0">Organiza los posts por tema.</p>
        </div>
    </div>

    <livewire:admin.categories.index />
@endsection
