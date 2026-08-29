@extends('layouts.app')

@section('title', 'Tonos')

@section('content')
    <div class="mb-4">
        <h1 class="h2 fw-bold mb-1">Tonos</h1>
        <p class="text-muted mb-0">Gestiona los tonos musicales.</p>
    </div>

    <livewire:admin.tonos.index />
@endsection
