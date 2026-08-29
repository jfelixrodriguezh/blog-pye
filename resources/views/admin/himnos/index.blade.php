@extends('layouts.app')

@section('title', 'Himnos')

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Himnos</h1>
            <p class="text-muted mb-0">Gestiona los himnos de todos los himnarios.</p>
        </div>
        <a href="{{ route('admin.himnos.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nuevo Himno
        </a>
    </div>

    <livewire:admin.himnos.index />
@endsection
